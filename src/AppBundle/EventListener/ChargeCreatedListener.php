<?php

namespace AppBundle\EventListener;

namespace AppBundle\EventListener;

use AppBundle\Event\ChargeCreatedEvent;
use AppBundle\Job\AnalyzeChargeJob;

class ChargeCreatedListener
{
    private $job;

    public function __construct(AnalyzeChargeJob $job)
    {
        $this->job = $job;
    }

    /**
     * Charge creation should trigger analysis via an event listener
     *
     * @param ChargeCreatedEvent $event
     * @return void
     * @throws \nicoSWD\Rule\Parser\Exception\ParserException
     */
    public function onChargeCreated(ChargeCreatedEvent $event): void
    {
        $this->job->handle($event->getCharge());
    }
}