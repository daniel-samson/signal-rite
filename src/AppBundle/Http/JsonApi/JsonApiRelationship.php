<?php

namespace AppBundle\Http\JsonApi;

/**
 * JSON:API relationship object.
 *
 * A relationship object MUST contain at least one of: links, data, meta.
 *
 * @see https://jsonapi.org/format/#document-resource-object-relationships
 */
class JsonApiRelationship implements \JsonSerializable
{
    /** @var array|null */
    private $links;

    /** @var JsonApiResourceIdentifier|JsonApiResourceIdentifier[]|null */
    private $data;

    /** @var array|null */
    private $meta;

    /**
     * Create a to-one relationship.
     *
     * @param JsonApiResourceIdentifier|null $identifier
     * @return self
     */
    public static function toOne(?JsonApiResourceIdentifier $identifier): self
    {
        $relationship = new self();
        $relationship->data = $identifier;
        return $relationship;
    }

    /**
     * Create a to-many relationship.
     *
     * @param JsonApiResourceIdentifier[] $identifiers
     * @return self
     */
    public static function toMany(array $identifiers): self
    {
        $relationship = new self();
        $relationship->data = $identifiers;
        return $relationship;
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
     * @param JsonApiResourceIdentifier|JsonApiResourceIdentifier[]|null $data
     * @return self
     */
    public function setData($data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return JsonApiResourceIdentifier|JsonApiResourceIdentifier[]|null
     */
    public function getData()
    {
        return $this->data;
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
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        $relationship = [];

        if ($this->links !== null) {
            $relationship['links'] = $this->links;
        }

        if ($this->data !== null || $this->data === null) {
            // data can be null for empty to-one relationships
            if ($this->data === null) {
                $relationship['data'] = null;
            } elseif (is_array($this->data)) {
                $relationship['data'] = array_map(function (JsonApiResourceIdentifier $identifier) {
                    return $identifier->jsonSerialize();
                }, $this->data);
            } else {
                $relationship['data'] = $this->data->jsonSerialize();
            }
        }

        if ($this->meta !== null) {
            $relationship['meta'] = $this->meta;
        }

        return $relationship;
    }
}
