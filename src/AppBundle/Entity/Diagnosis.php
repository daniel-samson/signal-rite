<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Diagnosis entity representing an ICD-10 diagnosis code.
 *
 * Diagnosis codes establish medical necessity for procedures. A charge can have
 * multiple diagnoses, and each diagnosis can appear on multiple charges (many-to-many).
 * The Rules Engine validates diagnosis-procedure relationships.
 *
 * @see Charge for billable healthcare events
 */
class Diagnosis
{
    /**
     * @var int
     */
    private $id;

    /**
     * ICD-10 diagnosis code (e.g., E11.9, I10, J06.9).
     *
     * @var string
     */
    private $code;

    /**
     * Human-readable diagnosis description.
     *
     * @var string
     */
    private $description;

    /**
     * Collection of charges associated with this diagnosis.
     *
     * @var Collection|Charge[]
     */
    private $charges;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->charges = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get code.
     *
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Set code.
     *
     * @param string $code ICD-10 code
     *
     * @return Diagnosis
     */
    public function setCode(string $code): self
    {
        $this->code = strtoupper(trim($code));

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Diagnosis
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get charges.
     *
     * @return Collection|Charge[]
     */
    public function getCharges(): Collection
    {
        return $this->charges;
    }

    /**
     * Add charge.
     *
     * @param Charge $charge
     *
     * @return Diagnosis
     */
    public function addCharge(Charge $charge): self
    {
        if (!$this->charges->contains($charge)) {
            $this->charges[] = $charge;
            $charge->addDiagnosis($this);
        }

        return $this;
    }

    /**
     * Remove charge.
     *
     * @param Charge $charge
     *
     * @return Diagnosis
     */
    public function removeCharge(Charge $charge): self
    {
        if ($this->charges->removeElement($charge)) {
            $charge->removeDiagnosis($this);
        }

        return $this;
    }
}
