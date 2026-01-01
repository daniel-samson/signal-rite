<?php

namespace AppBundle\Http\JsonApi;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Factory for creating JSON:API compliant HTTP responses.
 *
 * @see https://jsonapi.org/format/
 */
class JsonApiResponseFactory
{
    private const CONTENT_TYPE = 'application/vnd.api+json';

    /**
     * Create a success response with a single resource.
     *
     * @param JsonApiResource $resource
     * @param int $statusCode
     * @param array|null $meta
     * @param array|null $links
     * @param JsonApiResource[]|null $included
     * @return JsonResponse
     */
    public function createResourceResponse(
        JsonApiResource $resource,
        int $statusCode = 200,
        ?array $meta = null,
        ?array $links = null,
        ?array $included = null
    ): JsonResponse {
        $document = new JsonApiDocument();
        $document->setData($resource);

        if ($meta !== null) {
            $document->setMeta($meta);
        }

        if ($links !== null) {
            $document->setLinks($links);
        }

        if ($included !== null) {
            $document->setIncluded($included);
        }

        return $this->createResponse($document, $statusCode);
    }

    /**
     * Create a success response with a collection of resources.
     *
     * @param JsonApiResource[] $resources
     * @param int $statusCode
     * @param array|null $meta
     * @param array|null $links
     * @param JsonApiResource[]|null $included
     * @return JsonResponse
     */
    public function createCollectionResponse(
        array $resources,
        int $statusCode = 200,
        ?array $meta = null,
        ?array $links = null,
        ?array $included = null
    ): JsonResponse {
        $document = new JsonApiDocument();
        $document->setData($resources);

        if ($meta !== null) {
            $document->setMeta($meta);
        }

        if ($links !== null) {
            $document->setLinks($links);
        }

        if ($included !== null) {
            $document->setIncluded($included);
        }

        return $this->createResponse($document, $statusCode);
    }

    /**
     * Create a 201 Created response for a newly created resource.
     *
     * @param JsonApiResource $resource
     * @param string|null $location URL of the created resource
     * @return JsonResponse
     */
    public function createCreatedResponse(JsonApiResource $resource, ?string $location = null): JsonResponse
    {
        $document = new JsonApiDocument();
        $document->setData($resource);

        $response = $this->createResponse($document, 201);

        if ($location !== null) {
            $response->headers->set('Location', $location);
        }

        return $response;
    }

    /**
     * Create a 204 No Content response.
     *
     * @return JsonResponse
     */
    public function createNoContentResponse(): JsonResponse
    {
        return new JsonResponse(null, 204, [
            'Content-Type' => self::CONTENT_TYPE,
        ]);
    }

    /**
     * Create an error response with a single error.
     *
     * @param JsonApiError $error
     * @param int|null $statusCode If null, uses the status from the error
     * @return JsonResponse
     */
    public function createErrorResponse(JsonApiError $error, ?int $statusCode = null): JsonResponse
    {
        $document = new JsonApiDocument();
        $document->addError($error);

        $httpStatus = $statusCode ?? (int) ($error->getStatus() ?? 500);

        return $this->createResponse($document, $httpStatus);
    }

    /**
     * Create an error response with multiple errors.
     *
     * @param JsonApiError[] $errors
     * @param int $statusCode
     * @return JsonResponse
     */
    public function createErrorsResponse(array $errors, int $statusCode): JsonResponse
    {
        $document = new JsonApiDocument();
        $document->setErrors($errors);

        return $this->createResponse($document, $statusCode);
    }

    /**
     * Create an error response from Symfony validation violations.
     *
     * @param ConstraintViolationListInterface $violations
     * @param int $statusCode
     * @return JsonResponse
     */
    public function createValidationErrorResponse(
        ConstraintViolationListInterface $violations,
        int $statusCode = 422
    ): JsonResponse {
        $document = new JsonApiDocument();

        foreach ($violations as $violation) {
            $pointer = $this->buildPointer($violation->getPropertyPath());

            $error = JsonApiError::validationError(
                $violation->getMessage(),
                $pointer
            );

            $document->addError($error);
        }

        return $this->createResponse($document, $statusCode);
    }

    /**
     * Create a 404 Not Found error response.
     *
     * @param string $resourceType
     * @param string|int $id
     * @return JsonResponse
     */
    public function createNotFoundResponse(string $resourceType, $id): JsonResponse
    {
        return $this->createErrorResponse(
            JsonApiError::notFound($resourceType, $id),
            404
        );
    }

    /**
     * Create a 400 Bad Request error response.
     *
     * @param string $detail
     * @return JsonResponse
     */
    public function createBadRequestResponse(string $detail): JsonResponse
    {
        return $this->createErrorResponse(
            JsonApiError::badRequest($detail),
            400
        );
    }

    /**
     * Create a 500 Internal Server Error response.
     *
     * @param string $detail
     * @return JsonResponse
     */
    public function createInternalErrorResponse(string $detail = 'An unexpected error occurred.'): JsonResponse
    {
        return $this->createErrorResponse(
            JsonApiError::internalError($detail),
            500
        );
    }

    /**
     * Create a JSON:API error response for JSON parsing errors.
     *
     * @param string $errorMessage
     * @return JsonResponse
     */
    public function createJsonParseErrorResponse(string $errorMessage): JsonResponse
    {
        $error = (new JsonApiError())
            ->setStatus('400')
            ->setCode('invalid_json')
            ->setTitle('Invalid JSON')
            ->setDetail($errorMessage);

        return $this->createErrorResponse($error, 400);
    }

    /**
     * Create a JsonResponse from a JsonApiDocument.
     *
     * @param JsonApiDocument $document
     * @param int $statusCode
     * @return JsonResponse
     */
    public function createResponse(JsonApiDocument $document, int $statusCode): JsonResponse
    {
        return new JsonResponse(
            $document->jsonSerialize(),
            $statusCode,
            ['Content-Type' => self::CONTENT_TYPE]
        );
    }

    /**
     * Build a JSON Pointer from a Symfony property path.
     *
     * Converts property paths like "procedureCode" or "diagnosisCodes[0]"
     * to JSON:API pointers like "/data/attributes/procedure_code" or "/data/attributes/diagnosis_codes/0"
     *
     * @param string $propertyPath
     * @return string
     */
    private function buildPointer(string $propertyPath): string
    {
        // Convert camelCase to snake_case
        $snakeCase = $this->camelToSnake($propertyPath);

        // Convert array notation [0] to /0
        $pointer = preg_replace('/\[(\d+)\]/', '/$1', $snakeCase);

        return '/data/attributes/' . $pointer;
    }

    /**
     * Convert camelCase to snake_case.
     *
     * @param string $value
     * @return string
     */
    private function camelToSnake(string $value): string
    {
        return strtolower(preg_replace('/[A-Z]/', '_$0', lcfirst($value)));
    }
}
