<?php

namespace Tests\AppBundle\Enum;

use AppBundle\Enums\ChargePayerTypeEnum;
use PHPUnit\Framework\TestCase;

/**
 * Test ChargePayerTypeEnum using PhpCompatible\Enum
 */
class ChargePayerTypeEnumTest extends TestCase
{
    public function testCanAccessCaseByName()
    {
        $case = ChargePayerTypeEnum::medicare();
        $this->assertEquals('medicare', $case->name);
        $this->assertEquals('MEDICARE', $case->value);
    }

    public function testCaseInsensitiveAccess()
    {
        $case1 = ChargePayerTypeEnum::MEDICARE();
        $case2 = ChargePayerTypeEnum::medicare();
        $case3 = ChargePayerTypeEnum::Medicare();

        $this->assertEquals($case1->value, $case2->value);
        $this->assertEquals($case1->value, $case3->value);
    }

    public function testValueExists()
    {
        $this->assertTrue(ChargePayerTypeEnum::exists('MEDICARE'));
        $this->assertTrue(ChargePayerTypeEnum::exists('medicare'));
        $this->assertTrue(ChargePayerTypeEnum::exists('  MEDICARE  '));
    }

    public function testValueNotExists()
    {
        $this->assertFalse(ChargePayerTypeEnum::exists('NOT_EXISTS'));
    }

    public function testTryFrom()
    {
        $case = ChargePayerTypeEnum::tryFrom('MEDICARE');
        $this->assertNotNull($case);
        $this->assertEquals('medicare', $case->name);
        $this->assertEquals('MEDICARE', $case->value);
    }

    public function testTryFromReturnsNullForInvalidValue()
    {
        $case = ChargePayerTypeEnum::tryFrom('NOT_EXISTS');
        $this->assertNull($case);
    }

    public function testFrom()
    {
        $case = ChargePayerTypeEnum::from('MEDICARE');
        $this->assertEquals('medicare', $case->name);
        $this->assertEquals('MEDICARE', $case->value);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFromThrowsOnInvalidValue()
    {
        ChargePayerTypeEnum::from('NOT_EXISTS');
    }

    public function testCases()
    {
        $cases = ChargePayerTypeEnum::cases();
        $this->assertCount(3, $cases);
    }

    public function testNormalize()
    {
        $this->assertEquals('MEDICARE', ChargePayerTypeEnum::normalize('medicare'));
        $this->assertEquals('MEDICARE', ChargePayerTypeEnum::normalize('  MEDICARE  '));
    }

    public function testAllCasesHaveCorrectValues()
    {
        $this->assertEquals('MEDICARE', ChargePayerTypeEnum::medicare()->value);
        $this->assertEquals('MEDICAID', ChargePayerTypeEnum::medicaid()->value);
        $this->assertEquals('COMMERCIAL', ChargePayerTypeEnum::commercial()->value);
    }
}
