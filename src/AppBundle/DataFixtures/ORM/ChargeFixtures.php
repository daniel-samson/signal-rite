<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Charge;
use AppBundle\Entity\Department;
use AppBundle\Entity\Diagnosis;
use AppBundle\Entity\Patient;
use AppBundle\Entity\ProcedureCode;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class ChargeFixtures extends Fixture implements DependentFixtureInterface
{
    public const CHARGE_OFFICE_VISIT_MEDICARE = 'charge-office-visit-medicare';
    public const CHARGE_OFFICE_VISIT_MEDICAID = 'charge-office-visit-medicaid';
    public const CHARGE_OFFICE_VISIT_COMMERCIAL = 'charge-office-visit-commercial';
    public const CHARGE_MRI_BRAIN = 'charge-mri-brain';
    public const CHARGE_EMERGENCY_VISIT = 'charge-emergency-visit';
    public const CHARGE_HIGH_VALUE = 'charge-high-value';
    public const CHARGE_WITH_MODIFIER = 'charge-with-modifier';
    public const CHARGE_LAB_DRAW = 'charge-lab-draw';

    public function load(ObjectManager $manager)
    {
        // Standard Medicare office visit
        $charge1 = $this->createCharge(
            'MEDICARE',
            15000,
            '2025-01-15',
            [ProcedureCodeFixtures::PROCEDURE_CODE_99213],
            [DiagnosisFixtures::DIAGNOSIS_E11_9, DiagnosisFixtures::DIAGNOSIS_I10],
            DepartmentFixtures::DEPARTMENT_PRIMARY_CARE,
            PatientFixtures::PATIENT_MEDICARE_001
        );
        $manager->persist($charge1);
        $this->addReference(self::CHARGE_OFFICE_VISIT_MEDICARE, $charge1);

        // Medicaid office visit
        $charge2 = $this->createCharge(
            'MEDICAID',
            12000,
            '2025-01-16',
            [ProcedureCodeFixtures::PROCEDURE_CODE_99214],
            [DiagnosisFixtures::DIAGNOSIS_J06_9],
            DepartmentFixtures::DEPARTMENT_PRIMARY_CARE,
            PatientFixtures::PATIENT_MEDICAID_002
        );
        $manager->persist($charge2);
        $this->addReference(self::CHARGE_OFFICE_VISIT_MEDICAID, $charge2);

        // Commercial insurance office visit
        $charge3 = $this->createCharge(
            'COMMERCIAL',
            18000,
            '2025-01-17',
            [ProcedureCodeFixtures::PROCEDURE_CODE_99215],
            [DiagnosisFixtures::DIAGNOSIS_M54_5],
            DepartmentFixtures::DEPARTMENT_ORTHOPEDICS,
            PatientFixtures::PATIENT_COMMERCIAL_003
        );
        $manager->persist($charge3);
        $this->addReference(self::CHARGE_OFFICE_VISIT_COMMERCIAL, $charge3);

        // MRI Brain (radiology)
        $charge4 = $this->createCharge(
            'MEDICARE',
            250000,
            '2025-01-18',
            [ProcedureCodeFixtures::PROCEDURE_CODE_70553],
            [DiagnosisFixtures::DIAGNOSIS_R51, DiagnosisFixtures::DIAGNOSIS_G43_909],
            DepartmentFixtures::DEPARTMENT_RADIOLOGY,
            PatientFixtures::PATIENT_MEDICARE_001
        );
        $manager->persist($charge4);
        $this->addReference(self::CHARGE_MRI_BRAIN, $charge4);

        // Emergency visit
        $charge5 = $this->createCharge(
            'COMMERCIAL',
            85000,
            '2025-01-19',
            [ProcedureCodeFixtures::PROCEDURE_CODE_99285],
            [DiagnosisFixtures::DIAGNOSIS_R51],
            DepartmentFixtures::DEPARTMENT_EMERGENCY,
            PatientFixtures::PATIENT_OUTPATIENT_004
        );
        $manager->persist($charge5);
        $this->addReference(self::CHARGE_EMERGENCY_VISIT, $charge5);

        // High value charge (for threshold rules testing)
        $charge6 = $this->createCharge(
            'MEDICARE',
            1500000,
            '2025-01-20',
            [ProcedureCodeFixtures::PROCEDURE_CODE_99215],
            [DiagnosisFixtures::DIAGNOSIS_E11_9, DiagnosisFixtures::DIAGNOSIS_I10],
            DepartmentFixtures::DEPARTMENT_CARDIOLOGY,
            PatientFixtures::PATIENT_INPATIENT_005
        );
        $manager->persist($charge6);
        $this->addReference(self::CHARGE_HIGH_VALUE, $charge6);

        // Office visit with modifier 25 (for modifier rules testing)
        $charge7 = $this->createCharge(
            'MEDICARE',
            22000,
            '2025-01-21',
            [ProcedureCodeFixtures::PROCEDURE_CODE_99213_25, ProcedureCodeFixtures::PROCEDURE_CODE_36415],
            [DiagnosisFixtures::DIAGNOSIS_E11_9],
            DepartmentFixtures::DEPARTMENT_PRIMARY_CARE,
            PatientFixtures::PATIENT_MEDICARE_001
        );
        $manager->persist($charge7);
        $this->addReference(self::CHARGE_WITH_MODIFIER, $charge7);

        // Lab draw
        $charge8 = $this->createCharge(
            'MEDICAID',
            5000,
            '2025-01-22',
            [ProcedureCodeFixtures::PROCEDURE_CODE_36415],
            [DiagnosisFixtures::DIAGNOSIS_Z00_00],
            DepartmentFixtures::DEPARTMENT_LAB,
            PatientFixtures::PATIENT_MEDICAID_002
        );
        $manager->persist($charge8);
        $this->addReference(self::CHARGE_LAB_DRAW, $charge8);

        $manager->flush();
    }

    private function createCharge(
        string $payerType,
        int $chargeAmountCents,
        string $serviceDate,
        array $procedureCodeRefs,
        array $diagnosisRefs,
        string $departmentRef,
        string $patientRef
    ): Charge {
        $charge = new Charge();
        $charge->setPayerType($payerType);
        $charge->setChargeAmountCents($chargeAmountCents);
        $charge->setServiceDate(new \DateTimeImmutable($serviceDate));

        // Add procedure codes
        foreach ($procedureCodeRefs as $ref) {
            /** @var ProcedureCode $procedureCode */
            $procedureCode = $this->getReference($ref);
            $charge->addProcedureCode($procedureCode);
        }

        // Add diagnoses
        foreach ($diagnosisRefs as $ref) {
            /** @var Diagnosis $diagnosis */
            $diagnosis = $this->getReference($ref);
            $charge->addDiagnosis($diagnosis);
        }

        /** @var Department $department */
        $department = $this->getReference($departmentRef);
        $charge->setDepartment($department);

        /** @var Patient $patient */
        $patient = $this->getReference($patientRef);
        $charge->setPatient($patient);

        return $charge;
    }

    public function getDependencies()
    {
        return [
            ProcedureCodeFixtures::class,
            DiagnosisFixtures::class,
            DepartmentFixtures::class,
            PatientFixtures::class,
        ];
    }
}
