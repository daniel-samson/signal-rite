<?php

declare(strict_types=1);

namespace AppBundle\ValueObject;

use AppBundle\Entity\Charge;
use AppBundle\Entity\Rule;
use nicoSWD\Rule\Rule as ParserRule;

/**
 * Immutable value object encapsulating all data from a successful rule-charge analysis.
 *
 * This value object is created by the RiskAnalyzerService when a rule's condition
 * matches a charge. It bundles together all the components needed to create an
 * Insight entity:
 *
 * - The original Charge entity being analyzed
 * - The Rule entity that matched
 * - The parsed rule definition (condition, severity, message, etc.)
 * - The evaluation context (charge fields as key-value pairs)
 * - The php-rule-parser Rule instance (for potential re-evaluation)
 *
 * This value object is passed to Insight::createFromAnalyzeRuleForCharge() to
 * construct the resulting Insight entity.
 *
 * @see \AppBundle\Service\RiskAnalyzerService for creation
 * @see \AppBundle\Entity\Insight::createFromAnalyzeRuleForCharge() for consumption
 */
class AnalyzeRuleForChargeValueObject
{
    /**
     * The healthcare charge being analyzed.
     *
     * @var Charge
     */
    private $charge;

    /**
     * The php-rule-parser Rule instance with the evaluated condition.
     *
     * Retained in case re-evaluation or validation is needed.
     *
     * @var ParserRule
     */
    private $parserRule;

    /**
     * The Rule entity whose condition matched the charge.
     *
     * Used to link the generated Insight back to its source rule.
     *
     * @var Rule
     */
    private $ruleEntity;

    /**
     * Parsed rule definition containing insight output fields.
     *
     * @var RuleConditionValueObject
     */
    private $ruleDefinition;

    /**
     * Evaluation context - charge fields as key-value pairs.
     *
     * This is the array passed to php-rule-parser for condition evaluation.
     * Keys include: procedure_code, charge_amount, payer_type, diagnosis_codes, etc.
     *
     * @var array<string, mixed>
     */
    private $context;

    /**
     * Private constructor - use fromRiskEngine() factory method.
     *
     * @param Charge                   $charge
     * @param ParserRule               $parserRule
     * @param Rule                     $ruleEntity
     * @param RuleConditionValueObject $ruleDefinition
     * @param array                    $context
     */
    private function __construct(
        Charge $charge,
        ParserRule $parserRule,
        Rule $ruleEntity,
        RuleConditionValueObject $ruleDefinition,
        array $context
    ) {
        $this->charge = $charge;
        $this->parserRule = $parserRule;
        $this->ruleEntity = $ruleEntity;
        $this->ruleDefinition = $ruleDefinition;
        $this->context = $context;
    }

    /**
     * Create an AnalyzeRuleForChargeValueObject from the RiskAnalyzerService.
     *
     * Called when a rule's condition evaluates to true for a charge.
     *
     * @param Charge                   $charge         The charge being analyzed
     * @param ParserRule               $parserRule     The evaluated php-rule-parser instance
     * @param Rule                     $ruleEntity     The matching Rule entity
     * @param RuleConditionValueObject $ruleDefinition The parsed rule definition
     * @param array                    $context        The evaluation context array
     * @return self
     */
    public static function fromRiskEngine(
        Charge $charge,
        ParserRule $parserRule,
        Rule $ruleEntity,
        RuleConditionValueObject $ruleDefinition,
        array $context
    ): self {
        return new self($charge, $parserRule, $ruleEntity, $ruleDefinition, $context);
    }

    /**
     * Get the charge that was analyzed.
     *
     * @return Charge
     */
    public function getCharge(): Charge
    {
        return $this->charge;
    }

    /**
     * Get the php-rule-parser Rule instance.
     *
     * @return ParserRule
     */
    public function getParserRule(): ParserRule
    {
        return $this->parserRule;
    }

    /**
     * Get the Rule entity that matched.
     *
     * @return Rule
     */
    public function getRuleEntity(): Rule
    {
        return $this->ruleEntity;
    }

    /**
     * Get the parsed rule definition.
     *
     * @return RuleConditionValueObject
     */
    public function getRuleDefinition(): RuleConditionValueObject
    {
        return $this->ruleDefinition;
    }

    /**
     * Get the evaluation context array.
     *
     * @return array<string, mixed>
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Resolve the revenue at risk value from the evaluation context.
     *
     * Looks up the field name specified in the rule definition (e.g., 'charge_amount')
     * and returns its value from the context. If no field is specified or the field
     * doesn't exist in the context, falls back to the charge's chargeAmountCents.
     *
     * @return string The revenue at risk value in cents, as a string
     */
    public function resolveRevenueAtRiskInCents(): string
    {
        $field = $this->ruleDefinition->getRevenueAtRiskInCentsField();

        if ($field !== null && isset($this->context[$field])) {
            return (string) $this->context[$field];
        }

        // Fallback to charge amount
        return (string) $this->charge->getChargeAmountCents();
    }
}