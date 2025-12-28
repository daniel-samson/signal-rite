<?php

namespace Tests\AppBundle\Enum;

use AppBundle\Enums\ChargeProcedureCodeEnum;
use PHPUnit\Framework\TestCase;


class ChargeProcedureCodeEnumTest extends TestCase
{
    public function testIsProcedureCode() {
        // Valid codes and modifiers
        $this->assertEquals(true, ChargeProcedureCodeEnum::is('99213'));
        $this->assertEquals(true, ChargeProcedureCodeEnum::is('J1100'));
        $this->assertEquals(true, ChargeProcedureCodeEnum::is('99213-25'));
        $this->assertEquals(true, ChargeProcedureCodeEnum::is('99213-25-59'));
        $this->assertEquals(true, ChargeProcedureCodeEnum::is('a0428'));

        // Valid codes and modifiers
        $this->assertEquals(false, ChargeProcedureCodeEnum::is('ABC123'));
        $this->assertEquals(false, ChargeProcedureCodeEnum::is('1234'));
        $this->assertEquals(false, ChargeProcedureCodeEnum::is('99213-XXX'));
    }
}