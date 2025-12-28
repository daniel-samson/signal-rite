<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class EnumValue extends Constraint
{
    public $message = 'The value "{{ value }}" is not valid.';
    public $enumClass;

    public function getRequiredOptions()
    {
        return ['enumClass'];
    }

    public function validatedBy()
    {
        return EnumValueValidator::class;
    }
}
