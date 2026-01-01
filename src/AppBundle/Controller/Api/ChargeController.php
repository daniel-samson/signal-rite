<?php

declare(strict_types=1);

namespace AppBundle\Controller\Api;

use AppBundle\Dto\CreateChargeRequest;
use AppBundle\Event\ChargeCreatedEvent;
use AppBundle\Service\ChargeFactoryService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class ChargeController extends AbstractController
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var ChargeFactoryService
     */
    private $chargeFactoryService;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(LoggerInterface $logger, ChargeFactoryService $chargeFactoryService, EventDispatcherInterface $dispatcher, EventDispatcherInterface $eventDispatcher)
    {
        $this->logger = $logger;
        $this->chargeFactoryService = $chargeFactoryService;
        $this->dispatcher = $dispatcher;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @Route("/charge", methods={"POST"})
     */
    public function create(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new JsonResponse([
                [
                    'success'=> false,
                    'data' => [
                        'type' => 'ApiError',
                        'message' => 'Invalid JSON',
                        'reason' => json_last_error_msg(),
                    ],
                ],
                Response::HTTP_BAD_REQUEST,
            ]);
        }

        // validator Normalization
        $dto = new CreateChargeRequest();
        $dto->payerType = $data['payerType'];
        $dto->serviceDate = $data['serviceDate'];
        $dto->chargeAmountCents = $data['chargeAmountCents'];
        $dto->diagnosisCodes = $data['diagnosisCodes'];
        $errors = $this->get('validator')->validate($dto);

        if (count($errors) > 0) {
            return new JsonResponse([
                [
                    'success'=> false,
                    'data' => $errors
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            ]);
        }

        // Normalization
        $charge = $this->chargeFactoryService->createFromRequest($dto);

        // Persist + Flush
        $em = $this->getDoctrine()->getManager();
        $em->persist($charge);
        $em->flush();

        // Dispatch ChargeCreatedEvent event
        $this->eventDispatcher->dispatch(
            ChargeCreatedEvent::NAME,
            new ChargeCreatedEvent($charge)
        );

        // Success!
        return new JsonResponse([
            [
                'success'=> true,
                'data' =>  $charge->toHttpJsonResponseContext()
            ],
            Response::HTTP_CREATED,
        ]);
    }
}
