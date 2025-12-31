<?php

declare(strict_types=1);

namespace AppBundle\Enums;

use ReflectionClass;

abstract class ConstantEnum
{
    /**
     * Cache of constant maps per subclass.
     *
     * @var array<string, array{keyToValue: array<string,string>, valueToKey: array<string,string>}>
     */
    private static $cache = [];

    /**
     * Check whether a constant key exists.
     */
    public static function keyExists(string $key): bool
    {
        self::init(static::class);

        return array_key_exists($key, self::$cache[static::class]['keyToValue']);
    }

    /**
     * Check whether a value exists in this enum.
     */
    public static function exists(string $value): bool
    {
        return static::toKey($value) !== null;
    }

    /**
     * Convert a value to its constant key.
     *
     * Returns null if not found.
     */
    public static function toKey(string $value): ?string
    {
        self::init(static::class);

        $value = static::normalize($value);

        return self::$cache[static::class]['valueToKey'][$value] ?? null;
    }

    /**
     * Get all constant keys.
     *
     * @return string[]
     */
    public static function keys(): array
    {
        self::init(static::class);

        return array_keys(self::$cache[static::class]['keyToValue']);
    }

    /**
     * Get all constant values.
     *
     * @return string[]
     */
    public static function values(): array
    {
        self::init(static::class);

        return array_values(self::$cache[static::class]['keyToValue']);
    }

    /**
     * Normalise values for comparison.
     * Child classes may override.
     */
    public static function normalize(string $value): string
    {
        return strtoupper(trim($value));
    }

    /**
     * Build and cache maps for a subclass (once).
     */
    private static function init(string $class): void
    {
        if (isset(self::$cache[$class])) {
            return;
        }

        $reflection = new ReflectionClass($class);
        $constants  = $reflection->getConstants();

        $keyToValue = [];
        $valueToKey = [];

        foreach ($constants as $key => $value) {
            if (!is_string($value)) {
                continue;
            }

            $normalized = static::normalize($value);

            $keyToValue[$key] = $normalized;
            $valueToKey[$normalized] = $key;
        }

        self::$cache[$class] = [
            'keyToValue' => $keyToValue,
            'valueToKey' => $valueToKey,
        ];
    }

    /**
     * Get a map of constant names to human-friendly labels.
     *
     * @return array<string, string>
     */
    public static function labels(): array
    {
        $labels = [];

        foreach (self::keys() as $key) {
            $labels[$key] = self::humanize($key);
        }

        return $labels;
    }

    /**
     * Convert a CONSTANT_NAME into a human-friendly label.
     */
    private static function humanize(string $key): string
    {
        // Replace underscores with spaces
        $label = str_replace('_', ' ', $key);

        // Normalize spacing and case
        $label = strtolower($label);
        $label = ucwords($label);

        // Preserve common acronyms
        $label = str_replace(
            ['Cpt', 'Hcpcs', 'Ecg', 'Mri'],
            ['CPT', 'HCPCS', 'ECG', 'MRI'],
            $label
        );

        return $label;
    }
}
