<?php

namespace AppBundle\Http\JsonApi;

/**
 * JSON:API top-level document.
 *
 * A document MUST contain at least one of: data, errors, meta.
 * The members data and errors MUST NOT coexist in the same document.
 *
 * @see https://jsonapi.org/format/#document-top-level
 */
class JsonApiDocument implements \JsonSerializable
{
    /** @var JsonApiResource|JsonApiResource[]|JsonApiResourceIdentifier|JsonApiResourceIdentifier[]|null */
    private $data;

    /** @var JsonApiError[]|null */
    private $errors;

    /** @var array|null */
    private $meta;

    /** @var array|null */
    private $jsonapi;

    /** @var array|null */
    private $links;

    /** @var JsonApiResource[]|null */
    private $included;

    public function __construct()
    {
        $this->jsonapi = ['version' => '1.1'];
    }

    /**
     * @param JsonApiResource|JsonApiResource[]|JsonApiResourceIdentifier|JsonApiResourceIdentifier[]|null $data
     * @return self
     */
    public function setData($data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return JsonApiResource|JsonApiResource[]|JsonApiResourceIdentifier|JsonApiResourceIdentifier[]|null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param JsonApiError[] $errors
     * @return self
     */
    public function setErrors(array $errors): self
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * @return JsonApiError[]|null
     */
    public function getErrors(): ?array
    {
        return $this->errors;
    }

    /**
     * @param JsonApiError $error
     * @return self
     */
    public function addError(JsonApiError $error): self
    {
        if ($this->errors === null) {
            $this->errors = [];
        }
        $this->errors[] = $error;
        return $this;
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
     * @param array $links
     * @return self
     */
    public function setLinks(array $links): self
    {
        $this->links = $links;
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
     * @param JsonApiResource[] $included
     * @return self
     */
    public function setIncluded(array $included): self
    {
        $this->included = $included;
        return $this;
    }

    /**
     * @param JsonApiResource $resource
     * @return self
     */
    public function addIncluded(JsonApiResource $resource): self
    {
        if ($this->included === null) {
            $this->included = [];
        }
        $this->included[] = $resource;
        return $this;
    }

    /**
     * @return JsonApiResource[]|null
     */
    public function getIncluded(): ?array
    {
        return $this->included;
    }

    /**
     * Check if this is an error document.
     *
     * @return bool
     */
    public function hasErrors(): bool
    {
        return $this->errors !== null && count($this->errors) > 0;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        $document = [];

        if ($this->jsonapi !== null) {
            $document['jsonapi'] = $this->jsonapi;
        }

        if ($this->data !== null) {
            $document['data'] = $this->serializeData($this->data);
        } elseif ($this->errors !== null) {
            $document['errors'] = array_map(function (JsonApiError $error) {
                return $error->jsonSerialize();
            }, $this->errors);
        }

        if ($this->meta !== null) {
            $document['meta'] = $this->meta;
        }

        if ($this->links !== null) {
            $document['links'] = $this->links;
        }

        if ($this->included !== null && count($this->included) > 0) {
            $document['included'] = array_map(function (JsonApiResource $resource) {
                return $resource->jsonSerialize();
            }, $this->included);
        }

        return $document;
    }

    /**
     * @param JsonApiResource|JsonApiResource[]|JsonApiResourceIdentifier|JsonApiResourceIdentifier[]|null $data
     * @return array|null
     */
    private function serializeData($data)
    {
        if ($data === null) {
            return null;
        }

        if (is_array($data)) {
            return array_map(function ($item) {
                return $item->jsonSerialize();
            }, $data);
        }

        return $data->jsonSerialize();
    }
}
