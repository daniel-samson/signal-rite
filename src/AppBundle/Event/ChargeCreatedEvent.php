<?php

namespace AppBundle\Event;

use AppBundle\Entity\Charge;

class ChargeCreatedEvent extends \Symfony\Component\EventDispatcher\Event
{
    const NAME = 'charge.created';
    /**
     * @var Charge
     */
    private $charge;

    /**
     * @return Charge
     */
    public function getCharge(): Charge
    {
        return $this->charge;
    }

    public function __construct(Charge $charge)
    {
        $this->charge = $charge;
    }
}