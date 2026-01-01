<?php

namespace AppBundle\Http\JsonApi;

/**
 * JSON:API error object.
 *
 * @see https://jsonapi.org/format/#error-objects
 */
class JsonApiError implements \JsonSerializable
{
    /** @var string|null A unique identifier for this particular occurrence of the problem */
    private $id;

    /** @var array|null Links related to the error */
    private $links;

    /** @var string|null HTTP status code applicable to this problem */
    private $status;

    /** @var string|null Application-specific error code */
    private $code;

    /** @var string|null Short, human-readable summary of the problem */
    private $title;

    /** @var string|null Human-readable explanation specific to this occurrence */
    private $detail;

    /** @var array|null Object containing references to the source of the error */
    private $source;

    /** @var array|null Non-standard meta-information about the error */
    private $meta;

    /**
     * @param string|null $id
     * @return self
     */
    public function setId(?string $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
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
     * Set the "about" link - a link that leads to further details about this error.
     *
     * @param string $url
     * @return self
     */
    public function setAboutLink(string $url): self
    {
        if ($this->links === null) {
            $this->links = [];
        }
        $this->links['about'] = $url;
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
     * @param string $status
     * @return self
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string $code
     * @return self
     */
    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string $title
     * @return self
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $detail
     * @return self
     */
    public function setDetail(string $detail): self
    {
        $this->detail = $detail;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDetail(): ?string
    {
        return $this->detail;
    }

    /**
     * @param array $source
     * @return self
     */
    public function setSource(array $source): self
    {
        $this->source = $source;
        return $this;
    }

    /**
     * Set the source pointer (JSON Pointer to the value in the request document).
     *
     * @param string $pointer e.g. "/data/attributes/name"
     * @return self
     */
    public function setSourcePointer(string $pointer): self
    {
        if ($this->source === null) {
            $this->source = [];
        }
        $this->source['pointer'] = $pointer;
        return $this;
    }

    /**
     * Set the source parameter (query parameter that caused the error).
     *
     * @param string $parameter
     * @return self
     */
    public function setSourceParameter(string $parameter): self
    {
        if ($this->source === null) {
            $this->source = [];
        }
        $this->source['parameter'] = $parameter;
        return $this;
    }

    /**
     * Set the source header (HTTP header that caused the error).
     *
     * @param string $header
     * @return self
     */
    public function setSourceHeader(string $header): self
    {
        if ($this->source === null) {
            $this->source = [];
        }
        $this->source['header'] = $header;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getSource(): ?array
    {
        return $this->source;
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
        $error = [];

        if ($this->id !== null) {
            $error['id'] = $this->id;
        }

        if ($this->links !== null) {
            $error['links'] = $this->links;
        }

        if ($this->status !== null) {
            $error['status'] = $this->status;
        }

        if ($this->code !== null) {
            $error['code'] = $this->code;
        }

        if ($this->title !== null) {
            $error['title'] = $this->title;
        }

        if ($this->detail !== null) {
            $error['detail'] = $this->detail;
        }

        if ($this->source !== null) {
            $error['source'] = $this->source;
        }

        if ($this->meta !== null) {
            $error['meta'] = $this->meta;
        }

        return $error;
    }

    /**
     * Create a validation error.
     *
     * @param string $detail
     * @param string $pointer
     * @return self
     */
    public static function validationError(string $detail, string $pointer): self
    {
        return (new self())
            ->setStatus('422')
            ->setCode('validation_error')
            ->setTitle('Invalid Attribute')
            ->setDetail($detail)
            ->setSourcePointer($pointer);
    }

    /**
     * Create a not found error.
     *
     * @param string $resourceType
     * @param string|int $id
     * @return self
     */
    public static function notFound(string $resourceType, $id): self
    {
        return (new self())
            ->setStatus('404')
            ->setCode('not_found')
            ->setTitle('Resource Not Found')
            ->setDetail(sprintf('%s with id "%s" not found.', $resourceType, $id));
    }

    /**
     * Create a bad request error.
     *
     * @param string $detail
     * @return self
     */
    public static function badRequest(string $detail): self
    {
        return (new self())
            ->setStatus('400')
            ->setCode('bad_request')
            ->setTitle('Bad Request')
            ->setDetail($detail);
    }

    /**
     * Create an internal server error.
     *
     * @param string $detail
     * @return self
     */
    public static function internalError(string $detail = 'An unexpected error occurred.'): self
    {
        return (new self())
            ->setStatus('500')
            ->setCode('internal_error')
            ->setTitle('Internal Server Error')
            ->setDetail($detail);
    }
}
