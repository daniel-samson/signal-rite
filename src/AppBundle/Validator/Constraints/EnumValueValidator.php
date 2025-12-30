<?php

namespace AppBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class EnumValueValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     * @param EnumValue $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $enumClass = $constraint->enumClass;

        if (!method_exists($enumClass, 'exists') || !$enumClass::exists($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', (string) $value)
                ->addViolation();
        }
    }
}
