<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Department;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class DepartmentFixtures extends Fixture
{
    public const DEPARTMENT_CARDIOLOGY = 'department-cardiology';
    public const DEPARTMENT_ORTHOPEDICS = 'department-orthopedics';
    public const DEPARTMENT_EMERGENCY = 'department-emergency';
    public const DEPARTMENT_RADIOLOGY = 'department-radiology';
    public const DEPARTMENT_PRIMARY_CARE = 'department-primary-care';
    public const DEPARTMENT_NEUROLOGY = 'department-neurology';
    public const DEPARTMENT_LAB = 'department-lab';

    private const DEPARTMENTS = [
        [
            'code' => 'CARD',
            'name' => 'Cardiology',
            'ref' => self::DEPARTMENT_CARDIOLOGY,
        ],
        [
            'code' => 'ORTH',
            'name' => 'Orthopedics',
            'ref' => self::DEPARTMENT_ORTHOPEDICS,
        ],
        [
            'code' => 'EMER',
            'name' => 'Emergency Department',
            'ref' => self::DEPARTMENT_EMERGENCY,
        ],
        [
            'code' => 'RAD',
            'name' => 'Radiology',
            'ref' => self::DEPARTMENT_RADIOLOGY,
        ],
        [
            'code' => 'PCP',
            'name' => 'Primary Care',
            'ref' => self::DEPARTMENT_PRIMARY_CARE,
        ],
        [
            'code' => 'NEUR',
            'name' => 'Neurology',
            'ref' => self::DEPARTMENT_NEUROLOGY,
        ],
        [
            'code' => 'LAB',
            'name' => 'Laboratory',
            'ref' => self::DEPARTMENT_LAB,
        ],
    ];

    public function load(ObjectManager $manager)
    {
        foreach (self::DEPARTMENTS as $data) {
            $department = new Department();
            $department->setCode($data['code']);
            $department->setName($data['name']);

            $manager->persist($department);
            $this->addReference($data['ref'], $department);
        }

        $manager->flush();
    }
}
