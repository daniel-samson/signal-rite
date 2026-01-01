<?php

namespace AppBundle\Http\JsonApi\Transformer;

use AppBundle\Entity\Insight;
use AppBundle\Http\JsonApi\JsonApiRelationship;
use AppBundle\Http\JsonApi\JsonApiResource;
use AppBundle\Http\JsonApi\JsonApiResourceIdentifier;

/**
 * Transforms Insight entities to JSON:API resources.
 */
class InsightTransformer implements JsonApiTransformerInterface
{
    private const RESOURCE_TYPE = 'insights';

    /**
     * @param Insight $entity
     * @return JsonApiResource
     */
    public function transform($entity): JsonApiResource
    {
        $resource = new JsonApiResource(self::RESOURCE_TYPE, $entity->getId());

        $resource->setAttributes([
            'severity' => $entity->getSeverity(),
            'message' => $entity->getMessage(),
            'revenue_at_risk_cents' => $entity->getRevenueAtRiskInCents(),
            'created_at' => $entity->getCreatedAt() ? $entity->getCreatedAt()->format(\DateTime::ATOM) : null,
        ]);

        // Charge relationship (to-one)
        $charge = $entity->getCharge();
        if ($charge !== null) {
            $resource->setRelationship(
                'charge',
                JsonApiRelationship::toOne(
                    new JsonApiResourceIdentifier('charges', $charge->getId())
                )
            );
        }

        // Rule relationship (to-one)
        $rule = $entity->getRule();
        if ($rule !== null) {
            $resource->setRelationship(
                'rule',
                JsonApiRelationship::toOne(
                    new JsonApiResourceIdentifier('rules', $rule->getId())
                )
            );
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
}
