<?php

namespace AppBundle\Http\JsonApi\Transformer;

use AppBundle\Entity\Diagnosis;
use AppBundle\Http\JsonApi\JsonApiRelationship;
use AppBundle\Http\JsonApi\JsonApiResource;
use AppBundle\Http\JsonApi\JsonApiResourceIdentifier;

/**
 * Transforms Diagnosis entities to JSON:API resources.
 */
class DiagnosisTransformer implements JsonApiTransformerInterface
{
    private const RESOURCE_TYPE = 'diagnoses';

    /**
     * @param Diagnosis $entity
     * @return JsonApiResource
     */
    public function transform($entity): JsonApiResource
    {
        $resource = new JsonApiResource(self::RESOURCE_TYPE, $entity->getId());

        $resource->setAttributes([
            'code' => $entity->getCode(),
            'description' => $entity->getDescription(),
        ]);

        // Charges relationship (to-many)
        $chargeIdentifiers = [];
        foreach ($entity->getCharges() as $charge) {
            $chargeIdentifiers[] = new JsonApiResourceIdentifier('charges', $charge->getId());
        }

        if (count($chargeIdentifiers) > 0) {
            $resource->setRelationship(
                'charges',
                JsonApiRelationship::toMany($chargeIdentifiers)
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
