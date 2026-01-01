<?php

namespace AppBundle\Http\JsonApi\Transformer;

use AppBundle\Http\JsonApi\JsonApiResource;

/**
 * Interface for transforming entities to JSON:API resources.
 */
interface JsonApiTransformerInterface
{
    /**
     * Transform an entity to a JSON:API resource.
     *
     * @param object $entity
     * @return JsonApiResource
     */
    public function transform($entity): JsonApiResource;

    /**
     * Transform a collection of entities to JSON:API resources.
     *
     * @param iterable $entities
     * @return JsonApiResource[]
     */
    public function transformCollection(iterable $entities): array;

    /**
     * Get the resource type string.
     *
     * @return string
     */
    public function getResourceType(): string;
}
