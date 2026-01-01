<?php

namespace AppBundle\Http\JsonApi;

use AppBundle\Entity\Charge;
use AppBundle\Http\JsonApi\Transformer\ChargeTransformer;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Response builder for newly created Charge resources.
 */
class ChargeCreatedResponse
{
    /** @var JsonApiResponseFactory */
    private $responseFactory;

    /** @var ChargeTransformer */
    private $chargeTransformer;

    public function __construct(
        ?JsonApiResponseFactory $responseFactory = null,
        ?ChargeTransformer $chargeTransformer = null
    ) {
        $this->responseFactory = $responseFactory ?? new JsonApiResponseFactory();
        $this->chargeTransformer = $chargeTransformer ?? new ChargeTransformer();
    }

    /**
     * Create a 201 Created response for a newly created Charge.
     *
     * @param Charge $charge
     * @param string|null $location URL to the created resource
     * @param array $includes Related resources to include (e.g., ['patient', 'diagnoses'])
     * @return JsonResponse
     */
    public function create(Charge $charge, ?string $location = null, array $includes = []): JsonResponse
    {
        $resource = $this->chargeTransformer->transform($charge);
        $included = $this->chargeTransformer->getIncluded($charge, $includes);

        if (count($included) > 0) {
            $document = new JsonApiDocument();
            $document->setData($resource);
            $document->setIncluded($included);

            $response = $this->responseFactory->createResponse($document, 201);
        } else {
            $response = $this->responseFactory->createCreatedResponse($resource, $location);
        }

        if ($location !== null) {
            $response->headers->set('Location', $location);
        }

        return $response;
    }

    /**
     * Static factory method for quick usage.
     *
     * @param Charge $charge
     * @param string|null $location
     * @param array $includes
     * @return JsonResponse
     */
    public static function fromCharge(Charge $charge, ?string $location = null, array $includes = []): JsonResponse
    {
        $builder = new self();
        return $builder->create($charge, $location, $includes);
    }
}
