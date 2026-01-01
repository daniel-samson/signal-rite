<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Charge;
use AppBundle\Entity\Department;
use AppBundle\Entity\Diagnosis;
use AppBundle\Entity\Insight;
use AppBundle\Entity\Patient;
use AppBundle\Entity\ProcedureCode;
use DateTime;
use PHPUnit\Framework\TestCase;

class ChargeTest extends TestCase
{
    public function testCanCreateCharge()
    {
        $charge = new Charge();

        $this->assertInstanceOf(Charge::class, $charge);
    }

    public function testProcedureCodesCollectionIsInitialized()
    {
        $charge = new Charge();

        $this->assertCount(0, $charge->getProcedureCodes());
    }

    public function testCanAddProcedureCode()
    {
        $charge = new Charge();
        $procedureCode = new ProcedureCode();
        $procedureCode->setCode('99213');
        $procedureCode->setDescription('Office visit');

        $charge->addProcedureCode($procedureCode);

        $this->assertCount(1, $charge->getProcedureCodes());
        $this->assertTrue($charge->getProcedureCodes()->contains($procedureCode));
        $this->assertTrue($procedureCode->getCharges()->contains($charge));
    }

    public function testAddProcedureCodeIsIdempotent()
    {
        $charge = new Charge();
        $procedureCode = new ProcedureCode();
        $procedureCode->setCode('99213');

        $charge->addProcedureCode($procedureCode);
        $charge->addProcedureCode($procedureCode);

        $this->assertCount(1, $charge->getProcedureCodes());
    }

    public function testCanRemoveProcedureCode()
    {
        $charge = new Charge();
        $procedureCode = new ProcedureCode();
        $procedureCode->setCode('99213');

        $charge->addProcedureCode($procedureCode);
        $this->assertCount(1, $charge->getProcedureCodes());

        $charge->removeProcedureCode($procedureCode);
        $this->assertCount(0, $charge->getProcedureCodes());
        $this->assertFalse($procedureCode->getCharges()->contains($charge));
    }

    public function testHasModifierReturnsTrueWhenModifierPresent()
    {
        $charge = new Charge();
        $procedureCode = new ProcedureCode();
        $procedureCode->setCode('99213-25');

        $charge->addProcedureCode($procedureCode);

        $this->assertTrue($charge->hasModifier('25'));
        $this->assertFalse($charge->hasModifier('59'));
    }

    public function testHasModifierReturnsFalseWhenNoProcedureCodes()
    {
        $charge = new Charge();

        $this->assertFalse($charge->hasModifier('25'));
    }

    public function testCanSetAndGetChargeAmountCents()
    {
        $charge = new Charge();
        $charge->setChargeAmountCents(15000);

        $this->assertEquals(15000, $charge->getChargeAmountCents());
    }

    public function testCanSetAndGetPayerType()
    {
        $charge = new Charge();
        $charge->setPayerType('insurance');

        $this->assertEquals('INSURANCE', $charge->getPayerType());
    }

    public function testPayerTypeIsNormalized()
    {
        $charge = new Charge();
        $charge->setPayerType('  Self_Pay  ');

        $this->assertEquals('SELF_PAY', $charge->getPayerType());
    }

    public function testCanSetAndGetServiceDate()
    {
        $charge = new Charge();
        $date = new DateTime('2025-01-15');
        $charge->setServiceDate($date);

        $this->assertSame($date, $charge->getServiceDate());
        $this->assertEquals('2025-01-15', $charge->getServiceDate()->format('Y-m-d'));
    }

    public function testCanSetAndGetDepartment()
    {
        $department = new Department();
        $department->setCode('CARD');
        $department->setName('Cardiology');

        $charge = new Charge();
        $charge->setDepartment($department);

        $this->assertSame($department, $charge->getDepartment());
    }

    public function testCanSetAndGetPatient()
    {
        $patient = new Patient();
        $patient->setExternalId('PAT-001');

        $charge = new Charge();
        $charge->setPatient($patient);

        $this->assertSame($patient, $charge->getPatient());
    }

    public function testInsightsCollectionIsInitialized()
    {
        $charge = new Charge();

        $this->assertCount(0, $charge->getInsights());
    }

    public function testCanAddInsight()
    {
        $charge = new Charge();
        $insight = new Insight();
        $insight->setSeverity('high');
        $insight->setMessage('Test insight');

        $charge->addInsight($insight);

        $this->assertCount(1, $charge->getInsights());
        $this->assertTrue($charge->getInsights()->contains($insight));
        $this->assertSame($charge, $insight->getCharge());
    }

    public function testAddInsightIsIdempotent()
    {
        $charge = new Charge();
        $insight = new Insight();
        $insight->setSeverity('medium');
        $insight->setMessage('Test insight');

        $charge->addInsight($insight);
        $charge->addInsight($insight);

        $this->assertCount(1, $charge->getInsights());
    }

    public function testCanRemoveInsight()
    {
        $charge = new Charge();
        $insight = new Insight();
        $insight->setSeverity('low');
        $insight->setMessage('Test insight');

        $charge->addInsight($insight);
        $this->assertCount(1, $charge->getInsights());

        $charge->removeInsight($insight);
        $this->assertCount(0, $charge->getInsights());
        $this->assertNull($insight->getCharge());
    }

    public function testSettersReturnSelf()
    {
        $charge = new Charge();

        $this->assertSame($charge, $charge->setChargeAmountCents(100));
        $this->assertSame($charge, $charge->setPayerType('insurance'));
        $this->assertSame($charge, $charge->setServiceDate(new DateTime()));
    }

    public function testDiagnosesCollectionIsInitialized()
    {
        $charge = new Charge();

        $this->assertCount(0, $charge->getDiagnoses());
    }

    public function testCanAddDiagnosis()
    {
        $charge = new Charge();
        $diagnosis = new Diagnosis();
        $diagnosis->setCode('E11.9');
        $diagnosis->setDescription('Type 2 diabetes');

        $charge->addDiagnosis($diagnosis);

        $this->assertCount(1, $charge->getDiagnoses());
        $this->assertTrue($charge->getDiagnoses()->contains($diagnosis));
        $this->assertTrue($diagnosis->getCharges()->contains($charge));
    }

    public function testAddDiagnosisIsIdempotent()
    {
        $charge = new Charge();
        $diagnosis = new Diagnosis();
        $diagnosis->setCode('E11.9');

        $charge->addDiagnosis($diagnosis);
        $charge->addDiagnosis($diagnosis);

        $this->assertCount(1, $charge->getDiagnoses());
    }

    public function testCanRemoveDiagnosis()
    {
        $charge = new Charge();
        $diagnosis = new Diagnosis();
        $diagnosis->setCode('E11.9');

        $charge->addDiagnosis($diagnosis);
        $this->assertCount(1, $charge->getDiagnoses());

        $charge->removeDiagnosis($diagnosis);
        $this->assertCount(0, $charge->getDiagnoses());
        $this->assertFalse($diagnosis->getCharges()->contains($charge));
    }
}
