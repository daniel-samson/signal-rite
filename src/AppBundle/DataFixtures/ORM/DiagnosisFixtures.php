<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Diagnosis;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class DiagnosisFixtures extends Fixture
{
    public const DIAGNOSIS_E11_9 = 'diagnosis-e11-9';
    public const DIAGNOSIS_I10 = 'diagnosis-i10';
    public const DIAGNOSIS_J06_9 = 'diagnosis-j06-9';
    public const DIAGNOSIS_M54_5 = 'diagnosis-m54-5';
    public const DIAGNOSIS_R51 = 'diagnosis-r51';
    public const DIAGNOSIS_Z00_00 = 'diagnosis-z00-00';
    public const DIAGNOSIS_G43_909 = 'diagnosis-g43-909';

    /**
     * Common ICD-10 diagnosis codes for testing.
     */
    private const CODES = [
        // Endocrine
        [
            'code' => 'E11.9',
            'description' => 'Type 2 diabetes mellitus without complications',
            'ref' => self::DIAGNOSIS_E11_9,
        ],
        // Circulatory
        [
            'code' => 'I10',
            'description' => 'Essential (primary) hypertension',
            'ref' => self::DIAGNOSIS_I10,
        ],
        // Respiratory
        [
            'code' => 'J06.9',
            'description' => 'Acute upper respiratory infection, unspecified',
            'ref' => self::DIAGNOSIS_J06_9,
        ],
        // Musculoskeletal
        [
            'code' => 'M54.5',
            'description' => 'Low back pain',
            'ref' => self::DIAGNOSIS_M54_5,
        ],
        // Symptoms
        [
            'code' => 'R51',
            'description' => 'Headache',
            'ref' => self::DIAGNOSIS_R51,
        ],
        // Preventive
        [
            'code' => 'Z00.00',
            'description' => 'Encounter for general adult medical examination without abnormal findings',
            'ref' => self::DIAGNOSIS_Z00_00,
        ],
        // Nervous system
        [
            'code' => 'G43.909',
            'description' => 'Migraine, unspecified, not intractable, without status migrainosus',
            'ref' => self::DIAGNOSIS_G43_909,
        ],
    ];

    public function load(ObjectManager $manager)
    {
        foreach (self::CODES as $data) {
            $diagnosis = new Diagnosis();
            $diagnosis->setCode($data['code']);
            $diagnosis->setDescription($data['description']);

            $manager->persist($diagnosis);
            $this->addReference($data['ref'], $diagnosis);
        }

        $manager->flush();
    }
}
