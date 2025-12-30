<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Patient;
use DateTime;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class PatientTest extends TestCase
{
    public function testCanCreatePatient()
    {
        $patient = new Patient();

        $this->assertInstanceOf(Patient::class, $patient);
    }

    public function testCanSetAndGetExternalId()
    {
        $patient = new Patient();
        $patient->setExternalId('PAT-001');

        $this->assertEquals('PAT-001', $patient->getExternalId());
    }

    public function testExternalIdCanBeNull()
    {
        $patient = new Patient();
        $patient->setExternalId(null);

        $this->assertNull($patient->getExternalId());
    }

    public function testCanSetAndGetDateOfBirth()
    {
        $patient = new Patient();
        $dob = new DateTime('1985-06-15');
        $patient->setDateOfBirth($dob);

        $this->assertSame($dob, $patient->getDateOfBirth());
        $this->assertEquals('1985-06-15', $patient->getDateOfBirth()->format('Y-m-d'));
    }

    public function testCanSetAndGetSex()
    {
        $patient = new Patient();
        $patient->setSex('M');

        $this->assertEquals('M', $patient->getSex());
    }

    public function testCanSetSexFemale()
    {
        $patient = new Patient();
        $patient->setSex('F');

        $this->assertEquals('F', $patient->getSex());
    }

    public function testSetExternalIdReturnsSelf()
    {
        $patient = new Patient();

        $this->assertSame($patient, $patient->setExternalId('PAT-002'));
    }

    public function testSetDateOfBirthReturnsSelf()
    {
        $patient = new Patient();

        $this->assertSame($patient, $patient->setDateOfBirth(new DateTime()));
    }

    public function testSetSexReturnsSelf()
    {
        $patient = new Patient();

        $this->assertSame($patient, $patient->setSex('M'));
    }

    public function testCreatedAtTraitIsAvailable()
    {
        $patient = new Patient();
        $createdAt = new DateTimeImmutable('2025-01-01 12:00:00');
        $patient->setCreatedAt($createdAt);

        $this->assertSame($createdAt, $patient->getCreatedAt());
    }

    public function testOnPreCreatedAtSetsTimestamp()
    {
        $patient = new Patient();
        $this->assertNull($patient->getCreatedAt());

        $patient->onPreCreatedAt();

        $this->assertInstanceOf(DateTimeImmutable::class, $patient->getCreatedAt());
    }

    public function testOnPreCreatedAtDoesNotOverwrite()
    {
        $patient = new Patient();
        $originalDate = new DateTimeImmutable('2020-01-01 00:00:00');
        $patient->setCreatedAt($originalDate);

        $patient->onPreCreatedAt();

        $this->assertSame($originalDate, $patient->getCreatedAt());
    }

    public function testCanSetAllProperties()
    {
        $patient = new Patient();
        $patient->setExternalId('PAT-003');
        $patient->setDateOfBirth(new DateTime('1990-03-20'));
        $patient->setSex('F');

        $this->assertEquals('PAT-003', $patient->getExternalId());
        $this->assertEquals('1990-03-20', $patient->getDateOfBirth()->format('Y-m-d'));
        $this->assertEquals('F', $patient->getSex());
    }
}
