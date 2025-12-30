<?php

declare(strict_types=1);

namespace AppBundle\Enums;

use PhpCompatible\Enum\Enum;
use PhpCompatible\Enum\Value;

/**
 * Insight severity levels.
 *
 * @method static Value low()
 * @method static Value medium()
 * @method static Value high()
 * @method static Value critical()
 */
class InsightSeverityEnum extends Enum
{
    use NormalizesValuesTrait;

    /**
     * - Informational or minor issues
     * - No immediate financial or compliance risk
     */
    protected $low = 'LOW';

    /**
     * - Potential issue
     * - Requires review
     * - Could become a problem if ignored
     */
    protected $medium = 'MEDIUM';

    /**
     * - Significant issue
     * - Likely financial or compliance impact
     * - Should be acted on promptly
     */
    protected $high = 'HIGH';

    /**
     * - Immediate, high-impact risk
     * - Compliance, audit, or regulatory exposure
     * - Requires urgent action
     */
    protected $critical = 'CRITICAL';

    /**
     * LOW < MEDIUM < HIGH < CRITICAL
     *
     * @return int[] values in order of importance
     */
    public static function importance(): array
    {
        return [
            'LOW'      => 1,
            'MEDIUM'   => 2,
            'HIGH'     => 3,
            'CRITICAL' => 4,
        ];
    }

    /**
     * LOW = 1 < MEDIUM < HIGH < CRITICAL = 4
     *
     * @param string $severity
     * @return int returns the numerical importance
     */
    public static function weight(string $severity): int
    {
        return self::importance()[static::normalize($severity)];
    }
}
