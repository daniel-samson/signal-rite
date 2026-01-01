<?php

namespace AppBundle\Http\JsonApi\Transformer;

use AppBundle\Entity\Charge;
use AppBundle\Entity\Diagnosis;
use AppBundle\Entity\ProcedureCode;
use AppBundle\Http\JsonApi\JsonApiRelationship;
use AppBundle\Http\JsonApi\JsonApiResource;
use AppBundle\Http\JsonApi\JsonApiResourceIdentifier;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Transforms Charge entities to JSON:API resources.
 */
class ChargeTransformer implements JsonApiTransformerInterface
{
    private const RESOURCE_TYPE = 'charges';

    /** @var UrlGeneratorInterface|null */
    private $urlGenerator;

    public function __construct(?UrlGeneratorInterface $urlGenerator = null)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param Charge $entity
     * @return JsonApiResource
     */
    public function transform($entity): JsonApiResource
    {
        $resource = new JsonApiResource(self::RESOURCE_TYPE, $entity->getId());

        $resource->setAttributes([
            'charge_amount_cents' => $entity->getChargeAmountCents(),
            'payer_type' => $entity->getPayerType(),
            'service_date' => $entity->getServiceDate()->format('Y-m-d'),
            'created_at' => $entity->getCreatedAt() ? $entity->getCreatedAt()->format(\DateTime::ATOM) : null,
        ]);

        // Patient relationship (to-one)
        $patient = $entity->getPatient();
        if ($patient !== null) {
            $resource->setRelationship(
                'patient',
                JsonApiRelationship::toOne(
                    new JsonApiResourceIdentifier('patients', $patient->getId())
                )
            );
        }

        // Department relationship (to-one)
        $department = $entity->getDepartment();
        if ($department !== null) {
            $resource->setRelationship(
                'department',
                JsonApiRelationship::toOne(
                    new JsonApiResourceIdentifier('departments', $department->getId())
                )
            );
        }

        // Procedure codes relationship (to-many)
        $procedureCodeIdentifiers = [];
        foreach ($entity->getProcedureCodes() as $procedureCode) {
            $procedureCodeIdentifiers[] = new JsonApiResourceIdentifier(
                'procedure-codes',
                $procedureCode->getId()
            );
        }
        $resource->setRelationship(
            'procedure_codes',
            JsonApiRelationship::toMany($procedureCodeIdentifiers)
        );

        // Diagnoses relationship (to-many)
        $diagnosisIdentifiers = [];
        foreach ($entity->getDiagnoses() as $diagnosis) {
            $diagnosisIdentifiers[] = new JsonApiResourceIdentifier(
                'diagnoses',
                $diagnosis->getId()
            );
        }
        $resource->setRelationship(
            'diagnoses',
            JsonApiRelationship::toMany($diagnosisIdentifiers)
        );

        // Insights relationship (to-many)
        $insightIdentifiers = [];
        foreach ($entity->getInsights() as $insight) {
            $insightIdentifiers[] = new JsonApiResourceIdentifier(
                'insights',
                $insight->getId()
            );
        }
        $resource->setRelationship(
            'insights',
            JsonApiRelationship::toMany($insightIdentifiers)
        );

        // Add self link if URL generator is available
        if ($this->urlGenerator !== null && $entity->getId() !== null) {
            $resource->setLink('self', $this->urlGenerator->generate(
                'charge_show',
                ['id' => $entity->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            ));
        }

        return $resource;
    }

    /**
     * @inheritDoc
     */
    public function transformCollection(iterable $entities): array
    {
        $resources = [];
        foreach ($entities as $entity) {
            $resources[] = $this->transform($entity);
        }
        return $resources;
    }

    /**
     * @inheritDoc
     */
    public function getResourceType(): string
    {
        return self::RESOURCE_TYPE;
    }

    /**
     * Get included resources for a charge (related entities as full resources).
     *
     * @param Charge $charge
     * @param array $includes List of relationships to include (e.g., ['patient', 'diagnoses'])
     * @return JsonApiResource[]
     */
    public function getIncluded(Charge $charge, array $includes = []): array
    {
        $included = [];

        if (in_array('patient', $includes, true) && $charge->getPatient() !== null) {
            $patient = $charge->getPatient();
            $included[] = (new JsonApiResource('patients', $patient->getId()))
                ->setAttributes([
                    'external_id' => $patient->getExternalId(),
                    'date_of_birth' => $patient->getDateOfBirth()->format('Y-m-d'),
                    'sex' => $patient->getSex(),
                    'type' => $patient->getType(),
                ]);
        }

        if (in_array('department', $includes, true) && $charge->getDepartment() !== null) {
            $department = $charge->getDepartment();
            $included[] = (new JsonApiResource('departments', $department->getId()))
                ->setAttributes([
                    'code' => $department->getCode(),
                    'name' => $department->getName(),
                ]);
        }

        if (in_array('procedure_codes', $includes, true)) {
            foreach ($charge->getProcedureCodes() as $procedureCode) {
                $included[] = (new JsonApiResource('procedure-codes', $procedureCode->getId()))
                    ->setAttributes([
                        'code' => $procedureCode->getCode(),
                        'description' => $procedureCode->getDescription(),
                        'category' => $procedureCode->getCategory(),
                    ]);
            }
        }

        if (in_array('diagnoses', $includes, true)) {
            foreach ($charge->getDiagnoses() as $diagnosis) {
                $included[] = (new JsonApiResource('diagnoses', $diagnosis->getId()))
                    ->setAttributes([
                        'code' => $diagnosis->getCode(),
                        'description' => $diagnosis->getDescription(),
                    ]);
            }
        }

        if (in_array('insights', $includes, true)) {
            foreach ($charge->getInsights() as $insight) {
                $included[] = (new JsonApiResource('insights', $insight->getId()))
                    ->setAttributes([
                        'severity' => $insight->getSeverity(),
                        'message' => $insight->getMessage(),
                        'revenue_at_risk_cents' => $insight->getRevenueAtRiskInCents(),
                        'created_at' => $insight->getCreatedAt() ? $insight->getCreatedAt()->format(\DateTime::ATOM) : null,
                    ]);
            }
        }

        return $included;
    }
}
