<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Traits\CreatedAtTrait;
use DateTime;
use Doctrine\Common\Collections\Collection;

/**
 * Patient
 */
class Patient
{
    use CreatedAtTrait;

    /**
     * @var int
     */
    private $id;

    /**
     * @var string|null
     */
    private $externalId;

    /**
     * @var DateTime
     */
    private $dateOfBirth;

    /**
     * @var string
     */
    private $sex;

    /**
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
