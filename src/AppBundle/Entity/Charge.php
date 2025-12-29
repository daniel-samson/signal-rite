<?php

namespace AppBundle\Entity;

use AppBundle\Enums\ChargePayerTypeEnum;
use AppBundle\Enums\ChargeProcedureCodeEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


/**
 * Charge
 */
class Charge
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string CPT / HCPCS-style code
     */
    private $procedureCode;

    /**
     * @var int Charge amount in cents
     */
    private $chargeAmountCents;

    /**
     * @var string
     * @see ChargePayerTypeEnum
     */
    private $payerType;

    /**
     * @var \DateTime Date of service
     */
    private $serviceDate;

    /**
     * @var \DateTime Record creation time
     */
    private $createdAt;

    /**
     * @var Collection|Insight[]
     */
    private $insights;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->insights = new ArrayCollection();
    }


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
     * Set procedureCode.
     *  - Procedures
     *  - Services
     *  - Supplies
     *  - Non-physician services
     *
     * @param string $procedureCode CPT / HCPCS-style code
     * @See ChargeProcedureCodeEnum for valid code and validation
     *
     * @return Charge
     */
    public function setProcedureCode(string $procedureCode): self
    {
        $this->procedureCode = ChargeProcedureCodeEnum::normalize($procedureCode);

        return $this;
    }

    /**
     * Get procedureCode.
     *  - Procedures
     *  - Services
     *  - Supplies
     *  - Non-physician services
     *
     * @return string
     */
    public function getProcedureCode(): string
    {
        return $this->procedureCode;
    }

    /**
     * Set chargeAmountCents.
     *
     * @param int $chargeAmountCents
     *
     * @return Charge
     */
    public function setChargeAmountCents(int $chargeAmountCents): self
    {
        $this->chargeAmountCents = $chargeAmountCents;

        return $this;
    }

    /**
     * Get chargeAmountCents.
     *
     * @return int
     */
    public function getChargeAmountCents(): int
    {
        return $this->chargeAmountCents;
    }

    /**
     * Set payerType.
     *
     * @param string $payerType
     * @see ChargePayerTypeEnum
     *
     * @return Charge
     */
    public function setPayerType(string $payerType): self
    {
        $this->payerType = ChargePayerTypeEnum::normalize($payerType);

        return $this;
    }

    /**
     * Get payerType.
     *
     * @return string
     */
    public function getPayerType()
    {
        return $this->payerType;
    }

    /**
     * Set serviceDate.
     *
     * @param \DateTime $serviceDate
     *
     * @return Charge
     */
    public function setServiceDate(\DateTime $serviceDate): self
    {
        $this->serviceDate = $serviceDate;

        return $this;
    }

    /**
     * Get serviceDate.
     *
     * @return \DateTime
     */
    public function getServiceDate(): \DateTime
    {
        return $this->serviceDate;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Charge
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

    /**
     * Add insight.
     *
     * @param Insight $insight
     *
     * @return Charge
     */
    public function addInsight(Insight $insight): self
    {
        if (!$this->insights->contains($insight)) {
            $this->insights[] = $insight;
            $insight->setCharge($this);
        }

        return $this;
    }

    /**
     * Remove insight.
     *
     * @param Insight $insight
     *
     * @return Charge
     */
    public function removeInsight(Insight $insight): self
    {
        if ($this->insights->removeElement($insight)) {
            if ($insight->getCharge() === $this) {
                $insight->setCharge(null);
            }
        }

        return $this;
    }

    /**
     * Get insights.
     *
     * @return Collection|Insight[]
     */
    public function getInsights(): Collection
    {
        return $this->insights;
    }
}
