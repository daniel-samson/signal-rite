<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Patient;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class PatientFixtures extends Fixture
{
    public const PATIENT_MEDICARE_001 = 'patient-medicare-001';
    public const PATIENT_MEDICAID_002 = 'patient-medicaid-002';
    public const PATIENT_COMMERCIAL_003 = 'patient-commercial-003';
    public const PATIENT_OUTPATIENT_004 = 'patient-outpatient-004';
    public const PATIENT_INPATIENT_005 = 'patient-inpatient-005';

    private const PATIENTS = [
        [
            'externalId' => 'PAT-001',
            'dateOfBirth' => '1945-03-15',
            'sex' => 'M',
            'type' => 'OUTPATIENT',
            'ref' => self::PATIENT_MEDICARE_001,
        ],
        [
            'externalId' => 'PAT-002',
            'dateOfBirth' => '1990-07-22',
            'sex' => 'F',
            'type' => 'OUTPATIENT',
            'ref' => self::PATIENT_MEDICAID_002,
        ],
        [
            'externalId' => 'PAT-003',
            'dateOfBirth' => '1978-11-08',
            'sex' => 'M',
            'type' => 'OUTPATIENT',
            'ref' => self::PATIENT_COMMERCIAL_003,
        ],
        [
            'externalId' => 'PAT-004',
            'dateOfBirth' => '1985-06-30',
            'sex' => 'F',
            'type' => 'OUTPATIENT',
            'ref' => self::PATIENT_OUTPATIENT_004,
        ],
        [
            'externalId' => 'PAT-005',
            'dateOfBirth' => '1960-01-20',
            'sex' => 'M',
            'type' => 'INPATIENT',
            'ref' => self::PATIENT_INPATIENT_005,
        ],
    ];

    public function load(ObjectManager $manager)
    {
        foreach (self::PATIENTS as $data) {
            $patient = new Patient();
            $patient->setExternalId($data['externalId']);
            $patient->setDateOfBirth(new \DateTime($data['dateOfBirth']));
            $patient->setSex($data['sex']);
            $patient->setType($data['type']);

            $manager->persist($patient);
            $this->addReference($data['ref'], $patient);
        }

        $manager->flush();
    }
}
