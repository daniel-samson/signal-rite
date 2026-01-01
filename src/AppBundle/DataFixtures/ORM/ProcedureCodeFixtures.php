<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\ProcedureCode;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ProcedureCodeFixtures extends Fixture
{
    public const PROCEDURE_CODE_99213 = 'procedure-code-99213';
    public const PROCEDURE_CODE_99214 = 'procedure-code-99214';
    public const PROCEDURE_CODE_99215 = 'procedure-code-99215';
    public const PROCEDURE_CODE_99213_25 = 'procedure-code-99213-25';
    public const PROCEDURE_CODE_70553 = 'procedure-code-70553';
    public const PROCEDURE_CODE_70553_TC = 'procedure-code-70553-tc';
    public const PROCEDURE_CODE_70553_26 = 'procedure-code-70553-26';
    public const PROCEDURE_CODE_99285 = 'procedure-code-99285';
    public const PROCEDURE_CODE_36415 = 'procedure-code-36415';

    /**
     * Common CPT/HCPCS procedure codes for testing.
     */
    private const CODES = [
        // E&M Office Visits
        [
            'code' => '99213',
            'description' => 'Office visit, established patient, low complexity',
            'category' => 'E&M',
            'ref' => self::PROCEDURE_CODE_99213,
        ],
        [
            'code' => '99214',
            'description' => 'Office visit, established patient, moderate complexity',
            'category' => 'E&M',
            'ref' => self::PROCEDURE_CODE_99214,
        ],
        [
            'code' => '99215',
            'description' => 'Office visit, established patient, high complexity',
            'category' => 'E&M',
            'ref' => self::PROCEDURE_CODE_99215,
        ],
        // E&M with modifier
        [
            'code' => '99213-25',
            'description' => 'Office visit with significant, separately identifiable E&M service',
            'category' => 'E&M',
            'ref' => self::PROCEDURE_CODE_99213_25,
        ],
        // Radiology
        [
            'code' => '70553',
            'description' => 'MRI brain with and without contrast',
            'category' => 'Radiology',
            'ref' => self::PROCEDURE_CODE_70553,
        ],
        [
            'code' => '70553-TC',
            'description' => 'MRI brain - technical component only',
            'category' => 'Radiology',
            'ref' => self::PROCEDURE_CODE_70553_TC,
        ],
        [
            'code' => '70553-26',
            'description' => 'MRI brain - professional component only',
            'category' => 'Radiology',
            'ref' => self::PROCEDURE_CODE_70553_26,
        ],
        // Emergency
        [
            'code' => '99285',
            'description' => 'Emergency department visit, high severity',
            'category' => 'Emergency',
            'ref' => self::PROCEDURE_CODE_99285,
        ],
        // Lab
        [
            'code' => '36415',
            'description' => 'Collection of venous blood by venipuncture',
            'category' => 'Lab',
            'ref' => self::PROCEDURE_CODE_36415,
        ],
    ];

    public function load(ObjectManager $manager)
    {
        foreach (self::CODES as $data) {
            $procedureCode = new ProcedureCode();
            $procedureCode->setCode($data['code']);
            $procedureCode->setDescription($data['description']);
            $procedureCode->setCategory($data['category']);

            $manager->persist($procedureCode);
            $this->addReference($data['ref'], $procedureCode);
        }

        $manager->flush();
    }
}
