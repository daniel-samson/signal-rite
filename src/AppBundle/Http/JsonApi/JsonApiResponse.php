<?php

namespace AppBundle\Http\JsonApi;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * JSON:API compliant HTTP response.
 *
 * Extends Symfony's JsonResponse with the correct Content-Type header.
 *
 * @see https://jsonapi.org/format/#content-negotiation
 */
class JsonApiResponse extends JsonResponse
{
    private const CONTENT_TYPE = 'application/vnd.api+json';

    /**
     * @param JsonApiDocument|array|null $data
     * @param int $status
     * @param array $headers
     */
    public function __construct($data = null, int $status = 200, array $headers = [])
    {
        $headers['Content-Type'] = self::CONTENT_TYPE;

        if ($data instanceof JsonApiDocument) {
            $data = $data->jsonSerialize();
        }

        parent::__construct($data, $status, $headers);
    }

    /**
     * Create a response from a JsonApiDocument.
     *
     * @param JsonApiDocument $document
     * @param int $status
     * @return self
     */
    public static function fromDocument(JsonApiDocument $document, int $status = 200): self
    {
        return new self($document, $status);
    }

    /**
     * Create a success response with a single resource.
     *
     * @param JsonApiResource $resource
     * @param int $status
     * @return self
     */
    public static function resource(JsonApiResource $resource, int $status = 200): self
    {
        $document = new JsonApiDocument();
        $document->setData($resource);

        return self::fromDocument($document, $status);
    }

    /**
     * Create a success response with a collection of resources.
     *
     * @param JsonApiResource[] $resources
     * @param int $status
     * @return self
     */
    public static function collection(array $resources, int $status = 200): self
    {
        $document = new JsonApiDocument();
        $document->setData($resources);

        return self::fromDocument($document, $status);
    }

    /**
     * Create an error response.
     *
     * @param JsonApiError|JsonApiError[] $errors
     * @param int $status
     * @return self
     */
    public static function errors($errors, int $status = 400): self
    {
        $document = new JsonApiDocument();

        if ($errors instanceof JsonApiError) {
            $document->addError($errors);
        } else {
            $document->setErrors($errors);
        }

        return self::fromDocument($document, $status);
    }

    /**
     * Create a 201 Created response.
     *
     * @param JsonApiResource $resource
     * @param string|null $location
     * @return self
     */
    public static function created(JsonApiResource $resource, ?string $location = null): self
    {
        $response = self::resource($resource, 201);

        if ($location !== null) {
            $response->headers->set('Location', $location);
        }

        return $response;
    }

    /**
     * Create a 204 No Content response.
     *
     * @return self
     */
    public static function noContent(): self
    {
        return new self(null, 204);
    }
}
