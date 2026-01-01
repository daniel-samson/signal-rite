<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Charge;
use AppBundle\Entity\Department;
use AppBundle\Entity\Patient;
use AppBundle\Entity\ProcedureCode;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ChargeRelationshipTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager|null
     */
    private $entityManager;

    protected function setUp()
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }

    public function testSchemaIsValid()
    {
        $validator = new \Doctrine\ORM\Tools\SchemaValidator($this->entityManager);
        $errors = $validator->validateMapping();

        $this->assertEmpty($errors, 'Entity mapping errors: ' . print_r($errors, true));
    }

    public function testChargeDepartmentRelationship()
    {
        $department = new Department();
        $department->setCode('CARD');
        $department->setName('Cardiology');

        $charge = new Charge();
        $procedureCode = new ProcedureCode();
        $procedureCode->setCode('99213');
        $charge->addProcedureCode($procedureCode);
        $charge->setChargeAmountCents(15000);
        $charge->setPayerType('insurance');
        $charge->setServiceDate(new DateTime('2025-01-15'));
        $charge->setDepartment($department);

        $this->assertSame($department, $charge->getDepartment());
        $this->assertEquals('CARD', $charge->getDepartment()->getCode());
        $this->assertEquals('Cardiology', $charge->getDepartment()->getName());
    }

    public function testChargePatientRelationship()
    {
        $patient = new Patient();
        $patient->setExternalId('PAT-001');
        $patient->setDateOfBirth(new DateTime('1985-06-15'));
        $patient->setSex('M');

        $charge = new Charge();
        $procedureCode = new ProcedureCode();
        $procedureCode->setCode('99214');
        $charge->addProcedureCode($procedureCode);
        $charge->setChargeAmountCents(20000);
        $charge->setPayerType('self_pay');
        $charge->setServiceDate(new DateTime('2025-01-20'));
        $charge->setPatient($patient);

        $this->assertSame($patient, $charge->getPatient());
        $this->assertEquals('PAT-001', $charge->getPatient()->getExternalId());
        $this->assertEquals('M', $charge->getPatient()->getSex());
    }

    public function testChargeCanHaveBothDepartmentAndPatient()
    {
        $department = new Department();
        $department->setCode('ORTH');
        $department->setName('Orthopedics');

        $patient = new Patient();
        $patient->setExternalId('PAT-002');
        $patient->setDateOfBirth(new DateTime('1990-03-20'));
        $patient->setSex('F');

        $charge = new Charge();
        $procedureCode = new ProcedureCode();
        $procedureCode->setCode('99215');
        $charge->addProcedureCode($procedureCode);
        $charge->setChargeAmountCents(25000);
        $charge->setPayerType('medicare');
        $charge->setServiceDate(new DateTime('2025-01-25'));
        $charge->setDepartment($department);
        $charge->setPatient($patient);

        $this->assertSame($department, $charge->getDepartment());
        $this->assertSame($patient, $charge->getPatient());
        $this->assertEquals('ORTH', $charge->getDepartment()->getCode());
        $this->assertEquals('PAT-002', $charge->getPatient()->getExternalId());
    }

    public function testDepartmentMappingIsConfiguredCorrectly()
    {
        $metadata = $this->entityManager->getClassMetadata(Department::class);

        $this->assertTrue($metadata->hasAssociation('charges'));
        $this->assertEquals(
            Charge::class,
            $metadata->getAssociationTargetClass('charges')
        );
        $this->assertEquals(
            'department',
            $metadata->getAssociationMappedByTargetField('charges')
        );
    }

    public function testPatientMappingIsConfiguredCorrectly()
    {
        $metadata = $this->entityManager->getClassMetadata(Patient::class);

        $this->assertTrue($metadata->hasAssociation('charges'));
        $this->assertEquals(
            Charge::class,
            $metadata->getAssociationTargetClass('charges')
        );
        $this->assertEquals(
            'patient',
            $metadata->getAssociationMappedByTargetField('charges')
        );
    }

    public function testChargeMappingIsConfiguredCorrectly()
    {
        $metadata = $this->entityManager->getClassMetadata(Charge::class);

        // Check department association
        $this->assertTrue($metadata->hasAssociation('department'));
        $this->assertEquals(
            Department::class,
            $metadata->getAssociationTargetClass('department')
        );

        // Check patient association
        $this->assertTrue($metadata->hasAssociation('patient'));
        $this->assertEquals(
            Patient::class,
            $metadata->getAssociationTargetClass('patient')
        );

        // Check insights association
        $this->assertTrue($metadata->hasAssociation('insights'));
    }
}
