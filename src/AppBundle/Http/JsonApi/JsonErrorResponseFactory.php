<?php

namespace AppBundle\Http\JsonApi;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * @deprecated Use JsonApiResponseFactory instead.
 * @see JsonApiResponseFactory
 */
class JsonErrorResponseFactory
{
    /**
     * Build JSON API Response for Symfony validation errors.
     *
     * @deprecated Use JsonApiResponseFactory::createValidationErrorResponse() instead.
     *
     * @param ConstraintViolationListInterface $violations
     * @param int $httpStatusCode
     * @return JsonResponse
     */
    public static function fromViolations(ConstraintViolationListInterface $violations, int $httpStatusCode = 422): JsonResponse
    {
        $factory = new JsonApiResponseFactory();
        return $factory->createValidationErrorResponse($violations, $httpStatusCode);
    }

    /**
     * Build JSON API response for JSON parsing error message.
     *
     * @deprecated Use JsonApiResponseFactory::createJsonParseErrorResponse() instead.
     *
     * @param string $errorMessage
     * @return JsonResponse
     */
    public static function fromJsonParseErrorMessage(string $errorMessage): JsonResponse
    {
        $factory = new JsonApiResponseFactory();
        return $factory->createJsonParseErrorResponse($errorMessage);
    }
}
