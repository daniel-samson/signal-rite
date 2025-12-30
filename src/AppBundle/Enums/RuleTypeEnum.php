<?php

declare(strict_types=1);

namespace AppBundle\Enums;

use PhpCompatible\Enum\Enum;
use PhpCompatible\Enum\Value;

/**
 * Rule types for compliance and validation rules.
 *
 * @method static Value eligibility()
 * @method static Value pricing()
 * @method static Value validation()
 * @method static Value authorization()
 */
class RuleTypeEnum extends Enum
{
    use NormalizesValuesTrait;

    protected $eligibility = 'ELIGIBILITY';
    protected $pricing = 'PRICING';
    protected $validation = 'VALIDATION';
    protected $authorization = 'AUTHORIZATION';
}
