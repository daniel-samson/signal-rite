<?php

namespace AppBundle\Http\JsonApi\Transformer;

use AppBundle\Entity\ProcedureCode;
use AppBundle\Http\JsonApi\JsonApiRelationship;
use AppBundle\Http\JsonApi\JsonApiResource;
use AppBundle\Http\JsonApi\JsonApiResourceIdentifier;

/**
 * Transforms ProcedureCode entities to JSON:API resources.
 */
class ProcedureCodeTransformer implements JsonApiTransformerInterface
{
    private const RESOURCE_TYPE = 'procedure-codes';

    /**
     * @param ProcedureCode $entity
     * @return JsonApiResource
     */
    public function transform($entity): JsonApiResource
    {
        $resource = new JsonApiResource(self::RESOURCE_TYPE, $entity->getId());

        $resource->setAttributes([
            'code' => $entity->getCode(),
            'description' => $entity->getDescription(),
            'category' => $entity->getCategory(),
            'base_code' => $entity->getBaseCode(),
            'modifier' => $entity->getModifier(),
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
