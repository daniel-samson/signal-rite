<?php

declare(strict_types=1);

namespace AppBundle\Enums;

/**
 * Possible values for the Charge.payer_type
 */
class ChargePayerTypeEnum extends ConstantEnum
{
    public const MEDICARE = 'MEDICARE';
    public const MEDICAID = 'MEDICAID';
    public const COMMERCIAL = 'COMMERCIAL';
}