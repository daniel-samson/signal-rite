<?php

namespace AppBundle\EventListener;

use AppBundle\Event\InsightsGeneratedEvent;

class ChargeAnalysisNotificationListener
{
    public function onAnalysisCompleted(
        InsightsGeneratedEvent $event
    ): void {
        if (!$event->hasInsights()) {
            return;
        }

        $insights = $event->getInsights();

        if (count($insights) === 0) {
            return;
        }

        // TODO: Send ONE email with ALL relevant insights
    }
}