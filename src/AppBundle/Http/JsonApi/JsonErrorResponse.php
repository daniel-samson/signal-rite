<?php

namespace AppBundle\Http\JsonApi;

/**
 * @deprecated Use JsonApiDocument with errors instead.
 * @see JsonApiDocument
 */
class JsonErrorResponse
{
    /**
     * @deprecated
     * @var JsonApiError[]
     */
    public $errors = [];

    /**
     * Convert to JsonApiDocument.
     *
     * @return JsonApiDocument
     */
    public function toDocument(): JsonApiDocument
    {
        $document = new JsonApiDocument();
        $document->setErrors($this->errors);
        return $document;
    }
}
