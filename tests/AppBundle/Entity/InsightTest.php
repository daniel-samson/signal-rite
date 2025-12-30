<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Charge;
use AppBundle\Entity\Insight;
use AppBundle\Entity\Rule;
use DateTime;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class InsightTest extends TestCase
{
    public function testCanCreateInsight()
    {
        $insight = new Insight();

        $this->assertInstanceOf(Insight::class, $insight);
    }

    public function testCanSetAndGetSeverity()
    {
        $insight = new Insight();
        $insight->setSeverity('high');

        $this->assertEquals('HIGH', $insight->getSeverity());
    }

    public function testSeverityIsNormalized()
    {
        $insight = new Insight();
        $insight->setSeverity('  critical  ');

        $this->assertEquals('CRITICAL', $insight->getSeverity());
    }

    public function testCanSetAllSeverityLevels()
    {
        $insight = new Insight();

        $insight->setSeverity('low');
        $this->assertEquals('LOW', $insight->getSeverity());

        $insight->setSeverity('medium');
        $this->assertEquals('MEDIUM', $insight->getSeverity());

        $insight->setSeverity('high');
        $this->assertEquals('HIGH', $insight->getSeverity());

        $insight->setSeverity('critical');
        $this->assertEquals('CRITICAL', $insight->getSeverity());
    }

    public function testCanSetAndGetMessage()
    {
        $insight = new Insight();
        $insight->setMessage('Potential billing discrepancy detected');

        $this->assertEquals('Potential billing discrepancy detected', $insight->getMessage());
    }

    public function testCanSetAndGetRevenueAtRiskInCents()
    {
        $insight = new Insight();
        $insight->setRevenueAtRiskInCents('50000');

        $this->assertEquals('50000', $insight->getRevenueAtRiskInCents());
    }

    public function testCanSetAndGetCharge()
    {
        $charge = new Charge();
        $charge->setProcedureCode('99213');
        $charge->setChargeAmountCents(15000);
        $charge->setPayerType('insurance');
        $charge->setServiceDate(new DateTime('2025-01-15'));

        $insight = new Insight();
        $insight->setCharge($charge);

        $this->assertSame($charge, $insight->getCharge());
    }

    public function testChargeCanBeNull()
    {
        $insight = new Insight();
        $insight->setCharge(null);

        $this->assertNull($insight->getCharge());
    }

    public function testCanSetAndGetRule()
    {
        $rule = new Rule();
        $rule->setType('eligibility');
        $rule->setDescription('Test rule');
        $rule->setDefinitionYaml('test: true');
        $rule->setActive(true);

        $insight = new Insight();
        $insight->setRule($rule);

        $this->assertSame($rule, $insight->getRule());
    }

    public function testRuleCanBeNull()
    {
        $insight = new Insight();
        $insight->setRule(null);

        $this->assertNull($insight->getRule());
    }

    public function testSettersReturnSelf()
    {
        $insight = new Insight();

        $this->assertSame($insight, $insight->setSeverity('high'));
        $this->assertSame($insight, $insight->setMessage('Test'));
        $this->assertSame($insight, $insight->setRevenueAtRiskInCents('1000'));
        $this->assertSame($insight, $insight->setCharge(null));
        $this->assertSame($insight, $insight->setRule(null));
    }

    public function testCreatedAtTraitIsAvailable()
    {
        $insight = new Insight();
        $createdAt = new DateTimeImmutable('2025-01-01 12:00:00');
        $insight->setCreatedAt($createdAt);

        $this->assertSame($createdAt, $insight->getCreatedAt());
    }

    public function testOnPreCreatedAtSetsTimestamp()
    {
        $insight = new Insight();
        $this->assertNull($insight->getCreatedAt());

        $insight->onPreCreatedAt();

        $this->assertInstanceOf(DateTimeImmutable::class, $insight->getCreatedAt());
    }

    public function testCanCreateCompleteInsight()
    {
        $charge = new Charge();
        $charge->setProcedureCode('99214');
        $charge->setChargeAmountCents(20000);
        $charge->setPayerType('medicare');
        $charge->setServiceDate(new DateTime('2025-01-20'));

        $rule = new Rule();
        $rule->setType('pricing');
        $rule->setDescription('Price validation');
        $rule->setDefinitionYaml('check: price');
        $rule->setActive(true);

        $insight = new Insight();
        $insight->setSeverity('high');
        $insight->setMessage('Price exceeds expected range');
        $insight->setRevenueAtRiskInCents('5000');
        $insight->setCharge($charge);
        $insight->setRule($rule);

        $this->assertEquals('HIGH', $insight->getSeverity());
        $this->assertEquals('Price exceeds expected range', $insight->getMessage());
        $this->assertEquals('5000', $insight->getRevenueAtRiskInCents());
        $this->assertSame($charge, $insight->getCharge());
        $this->assertSame($rule, $insight->getRule());
    }

    public function testInsightChargeRelationshipBidirectional()
    {
        $charge = new Charge();
        $charge->setProcedureCode('99215');
        $charge->setChargeAmountCents(25000);
        $charge->setPayerType('insurance');
        $charge->setServiceDate(new DateTime('2025-01-25'));

        $insight = new Insight();
        $insight->setSeverity('medium');
        $insight->setMessage('Review recommended');

        // Add insight to charge (should set both sides)
        $charge->addInsight($insight);

        $this->assertSame($charge, $insight->getCharge());
        $this->assertTrue($charge->getInsights()->contains($insight));
    }

    public function testInsightRuleRelationshipBidirectional()
    {
        $rule = new Rule();
        $rule->setType('validation');
        $rule->setDescription('Test rule');
        $rule->setDefinitionYaml('test: true');
        $rule->setActive(true);

        $insight = new Insight();
        $insight->setSeverity('low');
        $insight->setMessage('Minor issue');

        // Add insight to rule (should set both sides)
        $rule->addInsight($insight);

        $this->assertSame($rule, $insight->getRule());
        $this->assertTrue($rule->getInsights()->contains($insight));
    }
}
