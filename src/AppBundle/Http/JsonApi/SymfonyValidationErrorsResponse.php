<?php

namespace AppBundle\Http\JsonApi;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * @deprecated Use JsonApiResponseFactory::createValidationErrorResponse() instead.
 * @see JsonApiResponseFactory
 */
final class SymfonyValidationErrorsResponse
{
    /**
     * Build JSON:API error response for validation errors.
     *
     * @deprecated Use JsonApiResponseFactory::createValidationErrorResponse() instead.
     *
     * @param ConstraintViolationListInterface $violations
     * @param int $statusCode
     * @return JsonResponse
     */
    public static function fromViolations(ConstraintViolationListInterface $violations, int $statusCode = 422): JsonResponse
    {
        $factory = new JsonApiResponseFactory();
        return $factory->createValidationErrorResponse($violations, $statusCode);
    }

    /**
     * @deprecated This method returns an incomplete array format. Use JsonApiResponseFactory instead.
     *
     * @param mixed $errors
     * @return array
     */
    public static function toJsonApiResponse($errors): array
    {
        return [
            'errors' => [
                [
                    'status' => '400',
                    'code' => 'invalid_json_in_request',
                    'title' => 'Invalid JSON',
                    'detail' => is_string($errors) ? $errors : '',
                ]
            ],
        ];
    }
}
