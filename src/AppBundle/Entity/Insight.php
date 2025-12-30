<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Traits\CreatedAtTrait;
use AppBundle\Enums\InsightSeverityEnum;

/**
 * Insight
 */
class Insight
{
    use CreatedAtTrait;
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $severity;

    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $revenueAtRiskInCents;

    /**
     * @var Charge
     */
    private $charge;

    /**
     * @var Rule
     */
    private $rule;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set severity.
     *
     * @param string $severity
     * @see InsightSeverityEnum
     *
     * @return Insight
     */
    public function setSeverity(string $severity): self
    {
        $this->severity = InsightSeverityEnum::normalize($severity);

        return $this;
    }

    /**
     * Get severity.
     *
     * @return string
     */
    public function getSeverity(): string
    {
        return $this->severity;
    }

    /**
     * Set message.
     *
     * @param string $message
     *
     * @return Insight
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message.
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set revenueAtRiskInCents.
     *
     * @param string $revenueAtRiskInCents
     *
     * @return Insight
     */
    public function setRevenueAtRiskInCents(string $revenueAtRiskInCents): self
    {
        $this->revenueAtRiskInCents = $revenueAtRiskInCents;

        return $this;
    }

    /**
     * Get revenueAtRiskInCents.
     *
     * @return string
     */
    public function getRevenueAtRiskInCents()
    {
        return $this->revenueAtRiskInCents;
    }

    /**
     * Set charge.
     *
     * @param Charge|null $charge
     *
     * @return Insight
     */
    public function setCharge(Charge $charge = null): self
    {
        $this->charge = $charge;

        return $this;
    }

    /**
     * Get charge.
     *
     * @return Charge|null
     */
    public function getCharge()
    {
        return $this->charge;
    }

    /**
     * Set rule.
     *
     * @param Rule|null $rule
     *
     * @return Insight
     */
    public function setRule(Rule $rule = null): self
    {
        $this->rule = $rule;

        return $this;
    }

    /**
     * Get rule.
     *
     * @return Rule|null
     */
    public function getRule()
    {
        return $this->rule;
    }
}
