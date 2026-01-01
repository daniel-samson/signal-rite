<?php

namespace AppBundle\Http\JsonApi;

/**
 * JSON:API resource object.
 *
 * A resource object MUST contain at least: type, id (except when creating).
 * MAY contain: attributes, relationships, links, meta.
 *
 * @see https://jsonapi.org/format/#document-resource-objects
 */
class JsonApiResource implements \JsonSerializable
{
    /** @var string */
    private $type;

    /** @var string|int|null */
    private $id;

    /** @var array|null */
    private $attributes;

    /** @var JsonApiRelationship[]|null */
    private $relationships;

    /** @var array|null */
    private $links;

    /** @var array|null */
    private $meta;

    /**
     * @param string $type
     * @param string|int|null $id
     */
    public function __construct(string $type, $id = null)
    {
        $this->type = $type;
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string|int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string|int $id
     * @return self
     */
    public function setId($id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param array $attributes
     * @return self
     */
    public function setAttributes(array $attributes): self
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function setAttribute(string $key, $value): self
    {
        if ($this->attributes === null) {
            $this->attributes = [];
        }
        $this->attributes[$key] = $value;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getAttributes(): ?array
    {
        return $this->attributes;
    }

    /**
     * @param string $name
     * @param JsonApiRelationship $relationship
     * @return self
     */
    public function setRelationship(string $name, JsonApiRelationship $relationship): self
    {
        if ($this->relationships === null) {
            $this->relationships = [];
        }
        $this->relationships[$name] = $relationship;
        return $this;
    }

    /**
     * @param JsonApiRelationship[] $relationships
     * @return self
     */
    public function setRelationships(array $relationships): self
    {
        $this->relationships = $relationships;
        return $this;
    }

    /**
     * @return JsonApiRelationship[]|null
     */
    public function getRelationships(): ?array
    {
        return $this->relationships;
    }

    /**
     * @param array $links
     * @return self
     */
    public function setLinks(array $links): self
    {
        $this->links = $links;
        return $this;
    }

    /**
     * @param string $name
     * @param string|array $link
     * @return self
     */
    public function setLink(string $name, $link): self
    {
        if ($this->links === null) {
            $this->links = [];
        }
        $this->links[$name] = $link;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getLinks(): ?array
    {
        return $this->links;
    }

    /**
     * @param array $meta
     * @return self
     */
    public function setMeta(array $meta): self
    {
        $this->meta = $meta;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getMeta(): ?array
    {
        return $this->meta;
    }

    /**
     * Get resource identifier (type + id only).
     *
     * @return JsonApiResourceIdentifier
     */
    public function toIdentifier(): JsonApiResourceIdentifier
    {
        return new JsonApiResourceIdentifier($this->type, $this->id);
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        $resource = [
            'type' => $this->type,
        ];

        if ($this->id !== null) {
            $resource['id'] = (string) $this->id;
        }

        if ($this->attributes !== null && count($this->attributes) > 0) {
            $resource['attributes'] = $this->attributes;
        }

        if ($this->relationships !== null && count($this->relationships) > 0) {
            $resource['relationships'] = [];
            foreach ($this->relationships as $name => $relationship) {
                $resource['relationships'][$name] = $relationship->jsonSerialize();
            }
        }

        if ($this->links !== null && count($this->links) > 0) {
            $resource['links'] = $this->links;
        }

        if ($this->meta !== null && count($this->meta) > 0) {
            $resource['meta'] = $this->meta;
        }

        return $resource;
    }
}
