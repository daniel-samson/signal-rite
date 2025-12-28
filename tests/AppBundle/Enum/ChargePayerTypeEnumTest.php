<?php

namespace Tests\AppBundle\Enum;

use AppBundle\Enums\ChargePayerTypeEnum;
use PHPUnit\Framework\TestCase;

/**
 * Test ChargePayerTypeEnum and ConstantEnum at the same time
 */
class ChargePayerTypeEnumTest extends TestCase
{
    public function testKeyExists()
    {
        $this->assertEquals(true, ChargePayerTypeEnum::keyExists("MEDICARE"));
    }

    public function testKeyNotExists()
    {
        $this->assertEquals(true, ChargePayerTypeEnum::keyExists("MEDICARE"));
    }

    public function testValueExists() {
        $this->assertEquals(true, ChargePayerTypeEnum::exists("MEDICARE"));
    }

    public function testValueNotExists() {
        $this->assertEquals(false, ChargePayerTypeEnum::exists("NOT_EXISTS"));
    }

    public function testToKey()
    {
        $this->assertEquals("MEDICARE", ChargePayerTypeEnum::toKey("MEDICARE"));
    }

    public function testToKeyNotExists() {
        $this->assertEquals(false, ChargePayerTypeEnum::toKey("NOT_EXISTS"));
    }

    public function testKeys()
    {
        $this->assertCount(3, ChargePayerTypeEnum::keys());
    }

    public function testValues()
    {
        $this->assertCount(3, ChargePayerTypeEnum::values());
    }

    public function testLabels() {
        $labels = ChargePayerTypeEnum::labels();
        $key = 'MEDICAID';
        $label = $labels[$key];

        $this->assertEquals("Medicaid", $label);
    }
}