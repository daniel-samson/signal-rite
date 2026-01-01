<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Traits\CreatedAtTrait;
use AppBundle\Enums\InsightSeverityEnum;
use AppBundle\ValueObject\AnalyzeRuleForChargeValueObject;

/**
 * Insight entity representing a rule-generated finding.
 *
 * Insights are the output of the Rules Engine. When a charge matches a rule's
 * conditions, an insight is created with severity, message, and optional revenue
 * impact. Insights provide explainable, actionable findings for revenue integrity.
 *
 * @see Rule for the triggering compliance rule
 * @see Charge for the evaluated healthcare charge
 * @see InsightSeverityEnum for severity levels (LOW, MEDIUM, HIGH, CRITICAL)
 */
class Insight
{
    use CreatedAtTrait;

    /**
     * @var int
     */
    private $id;

    /**
     * Severity level indicating urgency and impact.
     *
     * @var string
     * @see InsightSeverityEnum (LOW, MEDIUM, HIGH, CRITICAL)
     */
    private $severity;

    /**
     * Human-readable message explaining the finding.
     *
     * @var string
     */
    private $message;

    /**
     * Estimated revenue impact in cents.
     *
     * Used for prioritization and financial reporting.
     *
     * @var string
     */
    private $revenueAtRiskInCents;

    /**
     * The charge that triggered this insight.
     *
     * @var Charge
     */
    private $charge;

    /**
     * The rule that generated this insight.
     *
     * @var Rule
     */
    private $rule;

    /**
     * Create an Insight from an analyzed charge that matched a rule.
     *
     * @param AnalyzeRuleForChargeValueObject $analyzeRuleForCharge
     * @return self
     */
    public static function createFromAnalyzeRuleForCharge(AnalyzeRuleForChargeValueObject $analyzeRuleForCharge): self
    {
        $ruleDefinition = $analyzeRuleForCharge->getRuleDefinition();

        $self = new self();
        $self->setCharge($analyzeRuleForCharge->getCharge());
        $self->setRule($analyzeRuleForCharge->getRuleEntity());
        $self->setSeverity($ruleDefinition->getSeverity());
        $self->setMessage($ruleDefinition->getMessage());
        $self->setRevenueAtRiskInCents($analyzeRuleForCharge->resolveRevenueAtRiskInCents());

        return $self;
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
     * Get severity.
     *
     * @return string
     */
    public function getSeverity(): string
    {
        return $this->severity;
    }

    /**
     * Set severity.
     *
     * @param string $severity
     * @return Insight
     * @see InsightSeverityEnum
     *
     */
    public function setSeverity(string $severity): self
    {
        $this->severity = InsightSeverityEnum::normalize($severity);

        return $this;
    }

    /**
     * Get message.
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set message.
     *
     * @param string $message
     *
     * @return Insight
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get revenueAtRiskInCents.
     *
     * @return string
     */
    public function getRevenueAtRiskInCents()
    {
        return $this->revenueAtRiskInCents;
    }

    /**
     * Set revenueAtRiskInCents.
     *
     * @param string $revenueAtRiskInCents
     *
     * @return Insight
     */
    public function setRevenueAtRiskInCents(string $revenueAtRiskInCents): self
    {
        $this->revenueAtRiskInCents = $revenueAtRiskInCents;

        return $this;
    }

    /**
     * Get charge.
     *
     * @return Charge|null
     */
    public function getCharge()
    {
        return $this->charge;
    }

    /**
     * Set charge.
     *
     * @param Charge|null $charge
     *
     * @return Insight
     */
    public function setCharge(Charge $charge = null): self
    {
        $this->charge = $charge;

        return $this;
    }

    /**
     * Get rule.
     *
     * @return Rule|null
     */
    public function getRule()
    {
        return $this->rule;
    }

    /**
     * Set rule.
     *
     * @param Rule|null $rule
     *
     * @return Insight
     */
    public function setRule(Rule $rule = null): self
    {
        $this->rule = $rule;

        return $this;
    }
}
