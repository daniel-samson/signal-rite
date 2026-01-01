<?php

namespace AppBundle\Service;

use AppBundle\Entity\Charge;
use AppBundle\Entity\Insight;
use Doctrine\ORM\EntityManagerInterface;
use nicoSWD\Rule\Parser\Exception\ParserException;

class RiskAnalyzerService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var RuleEngineService
     */
    private $ruleEngineService;

    public function __construct(EntityManagerInterface $em, RuleEngineService $ruleEngineService)
    {
        $this->em = $em;
        $this->ruleEngineService = $ruleEngineService;
    }

    /**
     * Create insight from charge analysis
     * @param Charge $charge
     * @return Insight[]
     * @throws ParserException When rules are cannot be parsed.
     */
    public function createInsightsFromCharge(Charge $charge): array
    {
        $insights = [];

        // Load rules into memory
        $analyzeRulesForCharge = $this->ruleEngineService->getAnalyzeRulesForCharge($charge);



        /** @var  $rule */
        foreach ($analyzeRulesForCharge as $analyzeRuleForCharge) {
            if ($analyzeRuleForCharge->getParserRule()->isValid()) {
                $insight = Insight::createFromAnalyzeRuleForCharge($analyzeRuleForCharge);
                // persist entity
                $this->em->persist($insight);
                $this->em->flush();

                $insights[] = $insight;
            }
        }

        return $insights;
    }
}