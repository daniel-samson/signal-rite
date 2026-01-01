<?php

declare(strict_types=1);

namespace AppBundle\ValueObject;

use AppBundle\Entity\Rule;
use Symfony\Component\Yaml\Yaml;

/**
 * Immutable value object representing a parsed rule definition from YAML.
 *
 * This value object extracts and encapsulates the key fields from a Rule entity's
 * YAML definition that are needed for:
 * 1. Evaluating the rule condition against a charge (via php-rule-parser)
 * 2. Creating an Insight entity when the condition matches
 *
 * The YAML definition contains:
 * - condition: JavaScript-like expression evaluated by php-rule-parser
 * - severity: LOW, MEDIUM, HIGH, or CRITICAL (maps to InsightSeverityEnum)
 * - message: Human-readable explanation for the generated Insight
 * - revenue_at_risk_in_cents: Context field name to resolve for Insight.revenueAtRiskInCents
 *
 * @see Rule::getDefinitionYaml() for the source YAML
 * @see \nicoSWD\Rule\Rule for condition evaluation
 * @see \AppBundle\Entity\Insight for the resulting entity
 */
class RuleConditionValueObject
{
    /**
     * JavaScript-like condition expression for php-rule-parser.
     *
     * Example: 'procedure_code.startsWith("70") && charge_amount < 50000'
     *
     * @var string
     */
    private $condition;

    /**
     * Severity level for the generated Insight.
     *
     * @var string
     * @see \AppBundle\Enums\InsightSeverityEnum
     */
    private $severity;

    /**
     * Human-readable message explaining the finding.
     *
     * May contain ${variable} placeholders for interpolation.
     *
     * @var string
     */
    private $message;

    /**
     * Context field name to resolve for revenue at risk value.
     *
     * References a key in the charge context array (e.g., 'charge_amount').
     * If null, falls back to the charge's chargeAmountCents.
     *
     * @var string|null
     */
    private $revenueAtRiskInCentsField;

    /**
     * Private constructor - use fromRule() factory method.
     *
     * @param string      $condition
     * @param string      $severity
     * @param string      $message
     * @param string|null $revenueAtRiskInCentsField
     */
    private function __construct(
        string $condition,
        string $severity,
        string $message,
        ?string $revenueAtRiskInCentsField
    ) {
        $this->condition = $condition;
        $this->severity = $severity;
        $this->message = $message;
        $this->revenueAtRiskInCentsField = $revenueAtRiskInCentsField;
    }

    /**
     * Create a RuleConditionValueObject from a Rule entity.
     *
     * Parses the Rule's YAML definition and extracts the relevant fields.
     * Provides sensible defaults for missing fields:
     * - severity defaults to 'medium'
     * - message falls back to description, then 'Rule triggered'
     * - revenue_at_risk_in_cents defaults to null (uses charge amount)
     *
     * @param Rule $ruleEntity The Rule entity containing the YAML definition
     * @return self
     */
    public static function fromRule(Rule $ruleEntity): self
    {
        $definition = Yaml::parse($ruleEntity->getDefinitionYaml());

        return new self(
            $definition['condition'] ?? '',
            $definition['severity'] ?? 'medium',
            $definition['message'] ?? $definition['description'] ?? 'Rule triggered',
            $definition['revenue_at_risk_in_cents'] ?? null
        );
    }

    /**
     * Get the condition expression for php-rule-parser evaluation.
     *
     * @return string JavaScript-like condition expression
     */
    public function getCondition(): string
    {
        return $this->condition;
    }

    /**
     * Get the severity level for the generated Insight.
     *
     * @return string Severity level (LOW, MEDIUM, HIGH, CRITICAL)
     */
    public function getSeverity(): string
    {
        return $this->severity;
    }

    /**
     * Get the message for the generated Insight.
     *
     * @return string Human-readable finding message
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Get the context field name for resolving revenue at risk.
     *
     * @return string|null Field name from context, or null to use charge amount
     */
    public function getRevenueAtRiskInCentsField(): ?string
    {
        return $this->revenueAtRiskInCentsField;
    }
}