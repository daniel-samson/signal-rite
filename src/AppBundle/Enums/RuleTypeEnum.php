<?php

declare(strict_types=1);

namespace AppBundle\Enums;

use AppBundle\Enums\Traits\NormalizesValuesTrait;
use PhpCompatible\Enum\Enum;
use PhpCompatible\Enum\Value;

/**
 * Rule types for compliance and validation rules.
 *
 * @method static Value revenue()
 * @method static Value compliance()
 * @method static Value audit()
 */
class RuleTypeEnum extends Enum
{
    use NormalizesValuesTrait;

    protected $revenue = 'REVENUE';
    protected $compliance = 'COMPLIANCE';
    protected $audit = 'AUDIT';
}
