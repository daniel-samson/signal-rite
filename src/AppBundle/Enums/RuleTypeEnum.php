<?php

declare(strict_types=1);

namespace AppBundle\Enums;

class RuleTypeEnum extends ConstantEnum
{
    public const ELIGIBILITY = "ELIGIBILITY";
    public const PRICING = "PRICING";
    public const VALIDATION = "VALIDATION";
    public const AUTHORIZATION = "AUTHORIZATION";


}