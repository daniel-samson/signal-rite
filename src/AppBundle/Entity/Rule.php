<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Traits\CreatedAtTrait;
use AppBundle\Enums\RuleTypeEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Rule
 */
class Rule
{
    use CreatedAtTrait;

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
     * Add insight.
     *
     * @param Insight $insight
     *
     * @return Rule
     */
    public function addInsight(Insight $insight): self
    {
        if (!$this->insights->contains($insight)) {
            $this->insights[] = $insight;
            $insight->setRule($this);
        }

        return $this;
    }

    /**
     * Remove insight.
     *
     * @param Insight $insight
     *
     * @return Rule
     */
    public function removeInsight(Insight $insight): self
    {
        if ($this->insights->removeElement($insight)) {
            if ($insight->getRule() === $this) {
                $insight->setRule(null);
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
