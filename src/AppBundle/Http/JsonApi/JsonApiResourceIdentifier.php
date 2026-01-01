<?php

namespace AppBundle\Http\JsonApi;

/**
 * JSON:API resource identifier object.
 *
 * A "resource identifier object" is an object that identifies an individual resource.
 * It MUST contain type and id members.
 *
 * @see https://jsonapi.org/format/#document-resource-identifier-objects
 */
class JsonApiResourceIdentifier implements \JsonSerializable
{
    /** @var string */
    private $type;

    /** @var string|int */
    private $id;

    /** @var array|null */
    private $meta;

    /**
     * @param string $type
     * @param string|int $id
     */
    public function __construct(string $type, $id)
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
     * @return string|int
     */
    public function getId()
    {
        return $this->id;
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
        $identifier = [
            'type' => $this->type,
            'id' => (string) $this->id,
        ];

        if ($this->meta !== null && count($this->meta) > 0) {
            $identifier['meta'] = $this->meta;
        }

        return $identifier;
    }
}
