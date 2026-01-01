<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Charge;
use AppBundle\Entity\Diagnosis;
use AppBundle\Entity\ProcedureCode;
use PHPUnit\Framework\TestCase;

class DiagnosisTest extends TestCase
{
    public function testCanCreateDiagnosis()
    {
        $diagnosis = new Diagnosis();

        $this->assertInstanceOf(Diagnosis::class, $diagnosis);
    }

    public function testCanSetAndGetCode()
    {
        $diagnosis = new Diagnosis();
        $diagnosis->setCode('E11.9');

        $this->assertEquals('E11.9', $diagnosis->getCode());
    }

    public function testCodeIsNormalized()
    {
        $diagnosis = new Diagnosis();
        $diagnosis->setCode('  e11.9  ');

        $this->assertEquals('E11.9', $diagnosis->getCode());
    }

    public function testCanSetAndGetDescription()
    {
        $diagnosis = new Diagnosis();
        $diagnosis->setDescription('Type 2 diabetes mellitus without complications');

        $this->assertEquals('Type 2 diabetes mellitus without complications', $diagnosis->getDescription());
    }

    public function testChargesCollectionIsInitialized()
    {
        $diagnosis = new Diagnosis();

        $this->assertCount(0, $diagnosis->getCharges());
    }

    public function testCanAddCharge()
    {
        $diagnosis = new Diagnosis();
        $charge = new Charge();
        $procedureCode = new ProcedureCode();
        $procedureCode->setCode('99213');
        $charge->addProcedureCode($procedureCode);

        $diagnosis->addCharge($charge);

        $this->assertCount(1, $diagnosis->getCharges());
        $this->assertTrue($diagnosis->getCharges()->contains($charge));
        $this->assertTrue($charge->getDiagnoses()->contains($diagnosis));
    }

    public function testAddChargeIsIdempotent()
    {
        $diagnosis = new Diagnosis();
        $charge = new Charge();
        $procedureCode = new ProcedureCode();
        $procedureCode->setCode('99213');
        $charge->addProcedureCode($procedureCode);

        $diagnosis->addCharge($charge);
        $diagnosis->addCharge($charge);

        $this->assertCount(1, $diagnosis->getCharges());
    }

    public function testCanRemoveCharge()
    {
        $diagnosis = new Diagnosis();
        $charge = new Charge();
        $procedureCode = new ProcedureCode();
        $procedureCode->setCode('99213');
        $charge->addProcedureCode($procedureCode);

        $diagnosis->addCharge($charge);
        $this->assertCount(1, $diagnosis->getCharges());

        $diagnosis->removeCharge($charge);
        $this->assertCount(0, $diagnosis->getCharges());
        $this->assertFalse($charge->getDiagnoses()->contains($diagnosis));
    }

    public function testSettersReturnSelf()
    {
        $diagnosis = new Diagnosis();

        $this->assertSame($diagnosis, $diagnosis->setCode('E11.9'));
        $this->assertSame($diagnosis, $diagnosis->setDescription('Test'));
    }

    public function testManyToManyBidirectionalSync()
    {
        $diagnosis1 = new Diagnosis();
        $diagnosis1->setCode('E11.9');
        $diagnosis1->setDescription('Diabetes');

        $diagnosis2 = new Diagnosis();
        $diagnosis2->setCode('I10');
        $diagnosis2->setDescription('Hypertension');

        $charge = new Charge();
        $procedureCode = new ProcedureCode();
        $procedureCode->setCode('99214');
        $charge->addProcedureCode($procedureCode);

        // Add from charge side
        $charge->addDiagnosis($diagnosis1);
        $this->assertTrue($diagnosis1->getCharges()->contains($charge));
        $this->assertTrue($charge->getDiagnoses()->contains($diagnosis1));

        // Add from diagnosis side
        $diagnosis2->addCharge($charge);
        $this->assertTrue($charge->getDiagnoses()->contains($diagnosis2));
        $this->assertTrue($diagnosis2->getCharges()->contains($charge));

        // Both diagnoses linked to charge
        $this->assertCount(2, $charge->getDiagnoses());

        // Remove from charge side
        $charge->removeDiagnosis($diagnosis1);
        $this->assertFalse($diagnosis1->getCharges()->contains($charge));
        $this->assertFalse($charge->getDiagnoses()->contains($diagnosis1));

        // Remove from diagnosis side
        $diagnosis2->removeCharge($charge);
        $this->assertFalse($charge->getDiagnoses()->contains($diagnosis2));
        $this->assertFalse($diagnosis2->getCharges()->contains($charge));
    }
}
