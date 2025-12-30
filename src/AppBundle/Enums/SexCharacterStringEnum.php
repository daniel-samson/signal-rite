<?php

declare(strict_types=1);

namespace AppBundle\Enums;

use AppBundle\Enums\Traits\NormalizesValuesTrait;
use PhpCompatible\Enum\Enum;
use PhpCompatible\Enum\Value;

/**
 * Sex/Gender character codes.
 *
 * @method static Value male()
 * @method static Value female()
 * @method static Value other()
 */
class SexCharacterStringEnum extends Enum
{
    use NormalizesValuesTrait;

    protected $male = 'M';
    protected $female = 'F';
    protected $other = 'O';
}
