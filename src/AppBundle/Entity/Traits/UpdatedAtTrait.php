<?php

namespace AppBundle\Entity\Traits;

trait UpdatedAtTrait
{
    /**
     * @var \DateTimeImmutable|null
     */
    protected $updatedAt;

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function onPreUpdateAt(): void
    {
        $this->updatedAt = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
    }
}