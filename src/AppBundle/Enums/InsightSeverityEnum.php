<?php

declare(strict_types=1);

namespace AppBundle\Enums;

class InsightSeverityEnum extends ConstantEnum
{
    /**
     * - Informational or minor issues
     * - No immediate financial or compliance risk
     *
     * Often used for:
     * - FYI flags
     * - Minor variance
     * - Data quality hints
     */
    public const LOW = "LOW";
    /**
     * - Potential issue
     * - Requires review
     * - Could become a problem if ignored
     *
     * Often used for:
     * - Coding inconsistencies
     * - Documentation gaps
     * - Early compliance signals
     */
    public const MEDIUM = "MEDIUM";
    /**
     * - Significant issue
     * - Likely financial or compliance impact
     * - Should be acted on promptly
     *
     * Often used for:
     * - Incorrect billing patterns
     * - High-risk coding behavior
     * - Revenue leakage indicators
     */
    public const HIGH = "HIGH";
    /**
     * - Immediate, high-impact risk
     * - Compliance, audit, or regulatory exposure
     * - Requires urgent action
     *
     * Examples:
     * - Definite compliance violations
     * - Known audit triggers
     * - Severe revenue risk
     */
    public const CRITICAL = "CRITICAL";

    /**
     * LOW < MEDIUM < HIGH < CRITICAL
     * @return int[] values in order of importance
     */
    public static function importance(): array
    {
        return [
            self::LOW      => 1,
            self::MEDIUM   => 2,
            self::HIGH     => 3,
            self::CRITICAL => 4,
        ];
    }

    /**
     * LOW = 1 < MEDIUM < HIGH < CRITICAL = 4
     * @param string $severity
     * @return int returns the numerical importance
     */
    public static function weight(string $severity): int
    {
        return self::importance()[$severity];
    }
}