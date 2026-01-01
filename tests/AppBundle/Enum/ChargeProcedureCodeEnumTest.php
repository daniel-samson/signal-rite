<?php

namespace Tests\AppBundle\Enum;

use AppBundle\Enums\ProcedureCodeEnum;
use PHPUnit\Framework\TestCase;


class ChargeProcedureCodeEnumTest extends TestCase
{
    public function testIsProcedureCode() {
        // Valid codes and modifiers
        $this->assertEquals(true, ProcedureCodeEnum::is('99213'));
        $this->assertEquals(true, ProcedureCodeEnum::is('J1100'));
        $this->assertEquals(true, ProcedureCodeEnum::is('99213-25'));
        $this->assertEquals(true, ProcedureCodeEnum::is('99213-25-59'));
        $this->assertEquals(true, ProcedureCodeEnum::is('a0428'));

        // Valid codes and modifiers
        $this->assertEquals(false, ProcedureCodeEnum::is('ABC123'));
        $this->assertEquals(false, ProcedureCodeEnum::is('1234'));
        $this->assertEquals(false, ProcedureCodeEnum::is('99213-XXX'));
    }
}