<?php

declare(strict_types=1);

namespace AppBundle\Enums;

use AppBundle\Enums\Traits\NormalizesValuesTrait;
use PhpCompatible\Enum\Enum;
use PhpCompatible\Enum\Value;

/**
 * Possible values for the Charge.payer_type
 *
 * @method static Value medicare()
 * @method static Value medicaid()
 * @method static Value commercial()
 */
class ChargePayerTypeEnum extends Enum
{
    use NormalizesValuesTrait;

    protected $medicare = 'MEDICARE';
    protected $medicaid = 'MEDICAID';
    protected $commercial = 'COMMERCIAL';
}
