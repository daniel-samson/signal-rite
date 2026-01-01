<?php

namespace AppBundle\Service;

use AppBundle\Entity\Charge;
use AppBundle\Repository\RuleRepository;
use AppBundle\ValueObject\AnalyzeRuleForChargeValueObject;
use AppBundle\ValueObject\RuleConditionValueObject;
use nicoSWD\Rule\Parser\Exception\ParserException;
use nicoSWD\Rule\Rule;
use Psr\Log\LoggerInterface;
use Symfony\Component\Yaml\Yaml;

class RuleEngineService
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var RuleRepository
     */
    private $ruleRepository;
    /**
     * @var \AppBundle\Entity\Rule[]
     */
    private $ruleEntities;

    public function __construct(RuleRepository $ruleRepository, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->ruleRepository = $ruleRepository;
        $this->ruleEntities = $this->ruleRepository->findAll();
    }

    /**
     * Analyze rules for a specific charge
     *
     * It is called from the AnalyzeChargeJob
     *
     * @param Charge $chargeEntity
     * @return AnalyzeRuleForChargeValueObject[]
     * @throws ParserException
     */
    public function getAnalyzeRulesForCharge(Charge $chargeEntity): array
    {
        $rules = [];
        /** @var AnalyzeRuleForChargeValueObject $ruleEntity */
        foreach ($this->ruleEntities as $ruleEntity) {
            $rule = new Rule(
                RuleConditionValueObject::fromRule($ruleEntity)->getCondition(),
                $chargeEntity->toRuleContext()
            );

            if ($rule->isValid()) {
                $errorMessageLine = 'RuleEngineService: Failed to parse rule with '
                . 'ID: ' . $ruleEntity->getId() . ', '
                . 'Description: "' . $ruleEntity->getDescription() . '"';
                $this->logger->error($errorMessageLine);
                $this->logger->error($rule->getError());
                throw new ParserException($errorMessageLine);
            }

            $rules[] = AnalyzeRuleForChargeValueObject::fromRiskEngine($chargeEntity, $rule, $ruleEntity);
        }

        return $rules;
    }
}