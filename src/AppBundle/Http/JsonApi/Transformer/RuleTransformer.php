<?php

namespace AppBundle\Http\JsonApi\Transformer;

use AppBundle\Entity\Rule;
use AppBundle\Http\JsonApi\JsonApiRelationship;
use AppBundle\Http\JsonApi\JsonApiResource;
use AppBundle\Http\JsonApi\JsonApiResourceIdentifier;

/**
 * Transforms Rule entities to JSON:API resources.
 */
class RuleTransformer implements JsonApiTransformerInterface
{
    private const RESOURCE_TYPE = 'rules';

    /**
     * @param Rule $entity
     * @return JsonApiResource
     */
    public function transform($entity): JsonApiResource
    {
        $resource = new JsonApiResource(self::RESOURCE_TYPE, $entity->getId());

        $resource->setAttributes([
            'type' => $entity->getType(),
            'description' => $entity->getDescription(),
            'is_active' => $entity->isActive(),
            'created_at' => $entity->getCreatedAt() ? $entity->getCreatedAt()->format(\DateTime::ATOM) : null,
        ]);

        // Insights relationship (to-many)
        $insightIdentifiers = [];
        foreach ($entity->getInsights() as $insight) {
            $insightIdentifiers[] = new JsonApiResourceIdentifier('insights', $insight->getId());
        }

        if (count($insightIdentifiers) > 0) {
            $resource->setRelationship(
                'insights',
                JsonApiRelationship::toMany($insightIdentifiers)
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
