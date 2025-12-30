<?php

namespace AppBundle\Entity\Traits;

trait StampCreatedAtTrait
{
    /**
     * @var \DateTimeImmutable|null
     */
    protected $createdAt;

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function onPrePersist(): void
    {
        if ($this->createdAt === null) {
            $this->createdAt = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        }
    }
}