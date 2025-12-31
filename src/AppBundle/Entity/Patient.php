<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Traits\CreatedAtTrait;
use DateTime;
use Doctrine\Common\Collections\Collection;

/**
 * Patient entity representing a healthcare patient.
 *
 * A patient can have multiple charges associated with them. The patient record
 * stores demographic information used for eligibility checks and rule validation.
 *
 * @see Charge for billable healthcare events
 */
class Patient
{
    use CreatedAtTrait;

    /**
     * @var int
     */
    private $id;

    /**
     * External system identifier (e.g., MRN, EHR ID).
     *
     * @var string|null
     */
    private $externalId;

    /**
     * Patient date of birth, used for age-based rule validation.
     *
     * @var DateTime
     */
    private $dateOfBirth;

    /**
     * Patient sex (M, F, or O) for gender-specific procedure validation.
     *
     * @var string
     * @see SexCharacterStringEnum
     */
    private $sex;

    /**
     * Collection of charges billed for this patient.
     *
     * @var Collection|Charge[]
     */
    private $charges;

    /**
     * @return Charge[]|Collection
     */
    public function getCharges()
    {
        return $this->charges;
    }

    /**
     * @param Charge[]|Collection $charges
     */
    public function setCharges($charges): void
    {
        $this->charges = $charges;
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
     * Get externalId.
     *
     * @return string|null
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * Set externalId.
     *
     * @param string|null $externalId
     *
     * @return Patient
     */
    public function setExternalId($externalId = null)
    {
        $this->externalId = $externalId;

        return $this;
    }

    /**
     * Get dateOfBirth.
     *
     * @return DateTime
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    /**
     * Set dateOfBirth.
     *
     * @param DateTime $dateOfBirth
     *
     * @return Patient
     */
    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    /**
     * Get sex.
     *
     * @return string
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * Set sex.
     *
     * @param string $sex
     *
     * @return Patient
     */
    public function setSex($sex)
    {
        $this->sex = $sex;

        return $this;
    }
}
