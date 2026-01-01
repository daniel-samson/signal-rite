<?php

declare(strict_types=1);

namespace AppBundle\Controller\Api;

use AppBundle\Http\JsonApi\JsonApiResponseFactory;
use AppBundle\Http\JsonApi\Transformer\InsightTransformer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class InsightController extends AbstractController
{
    /** @var JsonApiResponseFactory */
    private $responseFactory;

    /** @var InsightTransformer */
    private $insightTransformer;

    public function __construct(
        JsonApiResponseFactory $responseFactory,
        InsightTransformer $insightTransformer
    ) {
        $this->responseFactory = $responseFactory;
        $this->insightTransformer = $insightTransformer;
    }

    /**
     * Returns the insights for a single charge.
     *
     * @Route("/charge/{id}/insights", name="charge_insights", methods={"GET"})
     */
    public function insightsOfCharge(int $id): JsonResponse
    {
        $charge = $this->getDoctrine()
            ->getRepository('AppBundle:Charge')
            ->find($id);

        if ($charge === null) {
            return $this->responseFactory->createNotFoundResponse('Charge', $id);
        }

        $insights = $charge->getInsights();
        $resources = $this->insightTransformer->transformCollection($insights);

        return $this->responseFactory->createCollectionResponse($resources);
    }

    /**
     * Returns a single insight.
     *
     * @Route("/insight/{id}", name="insight_show", methods={"GET"})
     */
    public function show(int $id): JsonResponse
    {
        $insight = $this->getDoctrine()
            ->getRepository('AppBundle:Insight')
            ->find($id);

        if ($insight === null) {
            return $this->responseFactory->createNotFoundResponse('Insight', $id);
        }

        $resource = $this->insightTransformer->transform($insight);

        return $this->responseFactory->createResourceResponse($resource);
    }

    /**
     * Returns all insights.
     *
     * @Route("/insights", name="insight_list", methods={"GET"})
     */
    public function list(): JsonResponse
    {
        $insights = $this->getDoctrine()
            ->getRepository('AppBundle:Insight')
            ->findAll();

        $resources = $this->insightTransformer->transformCollection($insights);

        return $this->responseFactory->createCollectionResponse($resources);
    }
}
