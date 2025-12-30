<?php

namespace AppBundle\Entity\Traits;

use DateTimeImmutable;
use DateTimeZone;

/**
 * Handles createdAt fields and onPrePersist
 */
trait CreatedAtTrait
{
    /**
     * @var DateTimeImmutable|null
     */
    protected $createdAt;

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function onPreCreatedAt(): void
    {
        if ($this->createdAt === null) {
            $this->createdAt = new DateTimeImmutable('now', new DateTimeZone('UTC'));
        }
    }
}