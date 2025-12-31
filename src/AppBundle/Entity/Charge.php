<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Traits\CreatedAtTrait;
use AppBundle\Enums\ChargePayerTypeEnum;
use AppBundle\Enums\ChargeProcedureCodeEnum;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


/**
 * Charge
 */
class Charge
{
    use CreatedAtTrait;

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
     * @var DateTime Date of service
     */
    private $serviceDate;

    /**
     * @var Collection|Insight[]
     */
    private $insights;

    /**
     * @var Collection|Diagnosis[]
     */
    private $diagnoses;

    /**
     * @var Department
     */
    private $department;
    /***
     * @var Patient
     */
    private $patient;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->insights = new ArrayCollection();
        $this->diagnoses = new ArrayCollection();
    }

    /**
     * @return Department
     */
    public function getDepartment(): Department
    {
        return $this->department;
    }

    /**
     * @param Department $department
     */
    public function setDepartment(Department $department): void
    {
        $this->department = $department;
    }

    /**
     * @return Patient
     */
    public function getPatient(): Patient
    {
        return $this->patient;
    }

    /**
     * @param Patient $patient
     */
    public function setPatient(Patient $patient): void
    {
        $this->patient = $patient;
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
     * Get chargeAmountCents.
     *
     * @return int
     */
    public function getChargeAmountCents(): int
    {
        return $this->chargeAmountCents;
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
     * Get payerType.
     *
     * @return string
     */
    public function getPayerType()
    {
        return $this->payerType;
    }

    /**
     * Set payerType.
     *
     * @param string $payerType
     * @return Charge
     * @see ChargePayerTypeEnum
     *
     */
    public function setPayerType(string $payerType): self
    {
        $this->payerType = ChargePayerTypeEnum::normalize($payerType);

        return $this;
    }

    /**
     * Get serviceDate.
     *
     * @return DateTime
     */
    public function getServiceDate(): DateTime
    {
        return $this->serviceDate;
    }

    /**
     * Set serviceDate.
     *
     * @param DateTime $serviceDate
     *
     * @return Charge
     */
    public function setServiceDate(DateTime $serviceDate): self
    {
        $this->serviceDate = $serviceDate;

        return $this;
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

    /**
     * Get diagnoses.
     *
     * @return Collection|Diagnosis[]
     */
    public function getDiagnoses(): Collection
    {
        return $this->diagnoses;
    }

    /**
     * Add diagnosis.
     *
     * @param Diagnosis $diagnosis
     *
     * @return Charge
     */
    public function addDiagnosis(Diagnosis $diagnosis): self
    {
        if (!$this->diagnoses->contains($diagnosis)) {
            $this->diagnoses[] = $diagnosis;
            $diagnosis->addCharge($this);
        }

        return $this;
    }

    /**
     * Remove diagnosis.
     *
     * @param Diagnosis $diagnosis
     *
     * @return Charge
     */
    public function removeDiagnosis(Diagnosis $diagnosis): self
    {
        if ($this->diagnoses->removeElement($diagnosis)) {
            $diagnosis->removeCharge($this);
        }

        return $this;
    }
}
