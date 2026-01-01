<?php

namespace AppBundle\Job;

use AppBundle\Entity\Charge;
use AppBundle\Event\InsightsGeneratedEvent;
use AppBundle\Service\RiskAnalyzerService;
use nicoSWD\Rule\Parser\Exception\ParserException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AnalyzeChargeJob
{
    private $riskAnalyzer;
    private $dispatcher;

    public function __construct(
        RiskAnalyzerService $riskAnalyzer,
        EventDispatcherInterface $dispatcher
    ) {
        $this->riskAnalyzer = $riskAnalyzer;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @throws ParserException
     */
    public function handle(Charge $charge): void
    {
        $insights = $this->riskAnalyzer->createInsightsFromCharge($charge);

        $this->dispatcher->dispatch(
            InsightsGeneratedEvent::NAME,
            new InsightsGeneratedEvent($charge, $insights)
        );
    }
}{

}