<?php

namespace AppBundle\Event;

use AppBundle\Entity\Charge;
use AppBundle\Entity\Insight;
use Symfony\Component\EventDispatcher\Event;

class InsightsGeneratedEvent extends Event
{
    const NAME = 'charge.analysis.completed';
    /**
     * @var Insight[]
     */
    private $insights;
    /**
     * @var Charge
     */
    private $charge;

    /**
     * @param Charge $charge
     * @param Insight[] $insights
     */
    public function __construct(Charge $charge, array $insights)
    {
        $this->insights = $insights;
        $this->charge = $charge;
    }

    /**
     * @return Insight[]
     */
    public function getInsights(): array
    {
        return $this->insights;
    }

    public function hasInsights(): bool
    {
        return count($this->insights) > 0;
    }

    /**
     * @return Charge
     */
    public function getCharge(): Charge
    {
        return $this->charge;
    }


}