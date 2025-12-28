<?php

namespace AppBundle\Entity;

use AppBundle\Enums\RuleTypeEnum;

/**
 * Rule
 */
class Rule
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $definitionYaml;

    /**
     * @var bool
     */
    private $active;

    /**
     * @var \DateTime
     */
    private $createdAt;


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
     * Set type.
     *
     * @param string $type
     *
     * @return Rule
     */
    public function setType(string $type): self
    {
        $this->type = RuleTypeEnum::normalize($type);

        return $this;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Rule
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

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
     * Set definitionYaml.
     *
     * @param string $definitionYaml
     *
     * @return Rule
     */
    public function setDefinitionYaml(string $definitionYaml): self
    {
        $this->definitionYaml = $definitionYaml;

        return $this;
    }

    /**
     * Get definitionYaml.
     *
     * @return string
     */
    public function getDefinitionYaml(): string
    {
        return $this->definitionYaml;
    }

    /**
     * Set active.
     *
     * @param bool $active
     *
     * @return Rule
     */
    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active.
     *
     * @return bool
     */
    public function getActive(): bool
    {
        return $this->active;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Rule
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
