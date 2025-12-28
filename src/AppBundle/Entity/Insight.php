<?php

namespace AppBundle\Entity;

use AppBundle\Enums\InsightSeverityEnum;

/**
 * Insight
 */
class Insight
{
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
     * @var \DateTime
     */
    private $createdAt;


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
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Insight
     */
    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
}
