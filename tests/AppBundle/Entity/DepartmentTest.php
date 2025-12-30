<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Department;
use PHPUnit\Framework\TestCase;

class DepartmentTest extends TestCase
{
    public function testCanCreateDepartment()
    {
        $department = new Department();

        $this->assertInstanceOf(Department::class, $department);
    }

    public function testCanSetAndGetCode()
    {
        $department = new Department();
        $department->setCode('CARD');

        $this->assertEquals('CARD', $department->getCode());
    }

    public function testCanSetAndGetName()
    {
        $department = new Department();
        $department->setName('Cardiology');

        $this->assertEquals('Cardiology', $department->getName());
    }

    public function testSetCodeReturnsSelf()
    {
        $department = new Department();

        $this->assertSame($department, $department->setCode('ORTH'));
    }

    public function testSetNameReturnsSelf()
    {
        $department = new Department();

        $this->assertSame($department, $department->setName('Orthopedics'));
    }

    public function testCanSetMultipleProperties()
    {
        $department = new Department();
        $department->setCode('NEUR');
        $department->setName('Neurology');

        $this->assertEquals('NEUR', $department->getCode());
        $this->assertEquals('Neurology', $department->getName());
    }
}
