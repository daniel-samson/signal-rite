<?php

namespace AppBundle\Validator\Constraints;

use AppBundle\Service\WhoIcdApiService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class Icd10Validator extends ConstraintValidator
{
    /**
     * @var WhoIcdApiService
     */
    private $whoIcdApiService;

    public function __construct(WhoIcdApiService $whoIcdApiService)
    {
        $this->whoIcdApiService = $whoIcdApiService;
    }

    public function validate($value, Constraint $constraint)
    {
        if (null === $value || '' === $value) {
            return;
        }

        if (!$this->whoIcdApiService->codeExists('2022', $value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ code }}', $value)
                ->addViolation();
        }
    }
}