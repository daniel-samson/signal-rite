<?php

declare(strict_types=1);

namespace AppBundle\Enums\Traits;

/**
 * Provides value normalization for enums with uppercase string values.
 */
trait NormalizesValuesTrait
{
    /**
     * Check whether a value exists in this enum (case-insensitive).
     *
     * @param string $value
     * @return bool
     */
    public static function exists(string $value): bool
    {
        return static::tryFrom(static::normalize($value)) !== null;
    }

    /**
     * Normalize a value for storage (uppercase, trimmed).
     *
     * @param string $value
     * @return string
     */
    public static function normalize(string $value): string
    {
        return strtoupper(trim($value));
    }
}
