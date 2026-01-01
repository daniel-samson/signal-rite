<?php

declare(strict_types=1);

namespace AppBundle\Controller\Api;

use AppBundle\Dto\CreateChargeRequest;
use AppBundle\Event\ChargeCreatedEvent;
use AppBundle\Http\JsonApi\JsonApiResponseFactory;
use AppBundle\Http\JsonApi\Transformer\ChargeTransformer;
use AppBundle\Service\ChargeFactoryService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api")
 */
class ChargeController extends AbstractController
{
    /** @var LoggerInterface */
    private $logger;

    /** @var ChargeFactoryService */
    private $chargeFactoryService;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var ValidatorInterface */
    private $validator;

    /** @var JsonApiResponseFactory */
    private $responseFactory;

    /** @var ChargeTransformer */
    private $chargeTransformer;

    public function __construct(
        LoggerInterface $logger,
        ChargeFactoryService $chargeFactoryService,
        EventDispatcherInterface $eventDispatcher,
        ValidatorInterface $validator,
        JsonApiResponseFactory $responseFactory,
        ChargeTransformer $chargeTransformer
    ) {
        $this->logger = $logger;
        $this->chargeFactoryService = $chargeFactoryService;
        $this->eventDispatcher = $eventDispatcher;
        $this->validator = $validator;
        $this->responseFactory = $responseFactory;
        $this->chargeTransformer = $chargeTransformer;
    }

    /**
     * @Route("/charge", name="charge_create", methods={"POST"})
     */
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->responseFactory->createJsonParseErrorResponse(json_last_error_msg());
        }

        // Hydrate DTO
        $dto = new CreateChargeRequest();
        $dto->payerType = $data['payerType'] ?? null;
        $dto->serviceDate = $data['serviceDate'] ?? null;
        $dto->chargeAmountCents = $data['chargeAmountCents'] ?? null;
        $dto->procedureCodes = $data['procedureCodes'] ?? [];
        $dto->diagnosisCodes = $data['diagnosisCodes'] ?? [];

        // Validate
        $violations = $this->validator->validate($dto);

        if (count($violations) > 0) {
            return $this->responseFactory->createValidationErrorResponse($violations);
        }

        // Create charge from DTO
        $charge = $this->chargeFactoryService->createFromRequest($dto);

        // Persist
        $em = $this->getDoctrine()->getManager();
        $em->persist($charge);
        $em->flush();

        // Dispatch ChargeCreatedEvent
        $this->eventDispatcher->dispatch(
            ChargeCreatedEvent::NAME,
            new ChargeCreatedEvent($charge)
        );

        // Return 201 Created with JSON:API resource
        $resource = $this->chargeTransformer->transform($charge);

        return $this->responseFactory->createCreatedResponse(
            $resource,
            $this->generateUrl('charge_show', ['id' => $charge->getId()], 0)
        );
    }

    /**
     * @Route("/charge/{id}", name="charge_show", methods={"GET"})
     */
    public function show(int $id): JsonResponse
    {
        $charge = $this->getDoctrine()
            ->getRepository('AppBundle:Charge')
            ->find($id);

        if ($charge === null) {
            return $this->responseFactory->createNotFoundResponse('Charge', $id);
        }

        $resource = $this->chargeTransformer->transform($charge);

        return $this->responseFactory->createResourceResponse($resource);
    }

    /**
     * @Route("/charges", name="charge_list", methods={"GET"})
     */
    public function list(): JsonResponse
    {
        $charges = $this->getDoctrine()
            ->getRepository('AppBundle:Charge')
            ->findAll();

        $resources = $this->chargeTransformer->transformCollection($charges);

        return $this->responseFactory->createCollectionResponse($resources);
    }
}
