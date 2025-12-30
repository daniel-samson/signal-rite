<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Insight;
use AppBundle\Entity\Rule;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class RuleTest extends TestCase
{
    public function testCanCreateRule()
    {
        $rule = new Rule();

        $this->assertInstanceOf(Rule::class, $rule);
    }

    public function testCanSetAndGetType()
    {
        $rule = new Rule();
        $rule->setType('eligibility');

        $this->assertEquals('ELIGIBILITY', $rule->getType());
    }

    public function testTypeIsNormalized()
    {
        $rule = new Rule();
        $rule->setType('  pricing  ');

        $this->assertEquals('PRICING', $rule->getType());
    }

    public function testCanSetAndGetDescription()
    {
        $rule = new Rule();
        $rule->setDescription('Check patient eligibility before service');

        $this->assertEquals('Check patient eligibility before service', $rule->getDescription());
    }

    public function testCanSetAndGetDefinitionYaml()
    {
        $rule = new Rule();
        $yaml = "conditions:\n  - check: eligibility\n    status: active";
        $rule->setDefinitionYaml($yaml);

        $this->assertEquals($yaml, $rule->getDefinitionYaml());
    }

    public function testCanSetAndGetActive()
    {
        $rule = new Rule();
        $rule->setActive(true);

        $this->assertTrue($rule->getActive());
    }

    public function testCanSetActiveToFalse()
    {
        $rule = new Rule();
        $rule->setActive(false);

        $this->assertFalse($rule->getActive());
    }

    public function testInsightsCollectionIsInitialized()
    {
        $rule = new Rule();

        $this->assertCount(0, $rule->getInsights());
    }

    public function testCanAddInsight()
    {
        $rule = new Rule();
        $insight = new Insight();
        $insight->setSeverity('high');
        $insight->setMessage('Rule violation detected');

        $rule->addInsight($insight);

        $this->assertCount(1, $rule->getInsights());
        $this->assertTrue($rule->getInsights()->contains($insight));
        $this->assertSame($rule, $insight->getRule());
    }

    public function testAddInsightIsIdempotent()
    {
        $rule = new Rule();
        $insight = new Insight();
        $insight->setSeverity('medium');
        $insight->setMessage('Test insight');

        $rule->addInsight($insight);
        $rule->addInsight($insight);

        $this->assertCount(1, $rule->getInsights());
    }

    public function testCanRemoveInsight()
    {
        $rule = new Rule();
        $insight = new Insight();
        $insight->setSeverity('low');
        $insight->setMessage('Test insight');

        $rule->addInsight($insight);
        $this->assertCount(1, $rule->getInsights());

        $rule->removeInsight($insight);
        $this->assertCount(0, $rule->getInsights());
        $this->assertNull($insight->getRule());
    }

    public function testSettersReturnSelf()
    {
        $rule = new Rule();

        $this->assertSame($rule, $rule->setType('validation'));
        $this->assertSame($rule, $rule->setDescription('Test'));
        $this->assertSame($rule, $rule->setDefinitionYaml('yaml: true'));
        $this->assertSame($rule, $rule->setActive(true));
    }

    public function testCreatedAtTraitIsAvailable()
    {
        $rule = new Rule();
        $createdAt = new DateTimeImmutable('2025-01-01 12:00:00');
        $rule->setCreatedAt($createdAt);

        $this->assertSame($createdAt, $rule->getCreatedAt());
    }

    public function testOnPreCreatedAtSetsTimestamp()
    {
        $rule = new Rule();
        $this->assertNull($rule->getCreatedAt());

        $rule->onPreCreatedAt();

        $this->assertInstanceOf(DateTimeImmutable::class, $rule->getCreatedAt());
    }

    public function testCanCreateCompleteRule()
    {
        $rule = new Rule();
        $rule->setType('authorization');
        $rule->setDescription('Verify prior authorization');
        $rule->setDefinitionYaml("check: prior_auth\nrequired: true");
        $rule->setActive(true);

        $this->assertEquals('AUTHORIZATION', $rule->getType());
        $this->assertEquals('Verify prior authorization', $rule->getDescription());
        $this->assertContains('prior_auth', $rule->getDefinitionYaml());
        $this->assertTrue($rule->getActive());
    }
}
