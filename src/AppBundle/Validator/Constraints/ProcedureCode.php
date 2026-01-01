<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class ProcedureCode extends Constraint
{
    public $message = "The procedure code '{{ procedureCode }}' is not valid.";

    public function validatedBy(): string
    {
        return ProcedureCodeValidator::class;
    }
}