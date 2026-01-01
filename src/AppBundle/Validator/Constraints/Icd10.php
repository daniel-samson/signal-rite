<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Validates that a value is a valid ICD-10 diagnosis code.
 *
 * Uses the WHO ICD API to verify the code exists.
 *
 * @Annotation
 */
class Icd10 extends Constraint
{
    public $message = 'The code "{{ code }}" is not a valid ICD-10 diagnosis code.';

    public function validatedBy()
    {
        return Icd10Validator::class;
    }
}
