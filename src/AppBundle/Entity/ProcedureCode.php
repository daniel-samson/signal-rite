<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * ProcedureCode entity representing a CPT/HCPCS billing code.
 *
 * CPT (Current Procedural Terminology) codes describe medical procedures and services.
 * HCPCS (Healthcare Common Procedure Coding System) codes include CPT plus additional
 * codes for supplies, equipment, and non-physician services.
 *
 * Codes are stored with modifiers as separate entries (like ICD-10 specificity):
 * - "99213" - Base E&M code
 * - "99213-25" - E&M with modifier 25 (separate identifiable service)
 * - "70553-TC" - MRI with technical component modifier
 *
 * @see Charge for billable healthcare events
 * @see Diagnosis for ICD-10 codes (similar many-to-many pattern)
 */
class ProcedureCode
{
    /**
     * @var int
     */
    private $id;

    /**
     * CPT/HCPCS code with optional modifier (e.g., "99213", "99213-25", "70553-TC").
     *
     * @var string
     */
    private $code;

    /**
     * Human-readable procedure description.
     *
     * @var string
     */
    private $description;

    /**
     * Procedure category for grouping and reporting.
     * Examples: E&M, Surgery, Radiology, Laboratory, Medicine
     *
     * @var string|null
     */
    private $category;

    /**
     * Collection of charges associated with this procedure code.
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
     * @param string $code CPT/HCPCS code (with optional modifier)
     *
     * @return ProcedureCode
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
     * @return ProcedureCode
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get category.
     *
     * @return string|null
     */
    public function getCategory(): ?string
    {
        return $this->category;
    }

    /**
     * Set category.
     *
     * @param string|null $category
     *
     * @return ProcedureCode
     */
    public function setCategory(?string $category): self
    {
        $this->category = $category;

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
     * @return ProcedureCode
     */
    public function addCharge(Charge $charge): self
    {
        if (!$this->charges->contains($charge)) {
            $this->charges[] = $charge;
            $charge->addProcedureCode($this);
        }

        return $this;
    }

    /**
     * Remove charge.
     *
     * @param Charge $charge
     *
     * @return ProcedureCode
     */
    public function removeCharge(Charge $charge): self
    {
        if ($this->charges->removeElement($charge)) {
            $charge->removeProcedureCode($this);
        }

        return $this;
    }

    /**
     * Get the base code without modifier.
     *
     * @return string
     */
    public function getBaseCode(): string
    {
        $parts = explode('-', $this->code);
        return $parts[0];
    }

    /**
     * Get the modifier portion of the code (if any).
     *
     * @return string|null
     */
    public function getModifier(): ?string
    {
        $parts = explode('-', $this->code);
        return isset($parts[1]) ? $parts[1] : null;
    }

    /**
     * Check if this code has a specific modifier.
     *
     * @param string $modifier The modifier to check (e.g., "25", "TC")
     *
     * @return bool
     */
    public function hasModifier(string $modifier): bool
    {
        $modifier = strtoupper(trim($modifier));
        return $this->getModifier() === $modifier;
    }
}
