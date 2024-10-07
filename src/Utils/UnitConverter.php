<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Utils;

use InvalidArgumentException;

class UnitConverter
{
    /**
     * Unit multipliers.
     */
    private const WEI_MULTIPLIER = 1;
    private const GWEI_MULTIPLIER = 1000000000; // 1e9
    private const ARK_MULTIPLIER = 1000000000000000000; // 1e18

    /**
     * Parse a value into the appropriate units.
     *
     * @param float|int|string $value
     * @param string $unit
     * @return string
     */
    public static function parseUnits($value, string $unit = 'ark'): string
    {
        switch (strtolower($unit)) {
            case 'wei':
                return bcmul((string) $value, (string) self::WEI_MULTIPLIER, 0);
            case 'gwei':
                return bcmul((string) $value, (string) self::GWEI_MULTIPLIER, 0);
            case 'ark':
                return bcmul((string) $value, (string) self::ARK_MULTIPLIER, 0);
            default:
                throw new InvalidArgumentException("Unsupported unit: {$unit}. Supported units are 'wei', 'gwei', and 'ark'.");
        }
    }

    /**
     * Format a value from smaller units to a larger unit.
     *
     * @param string $value
     * @param string $unit
     * @return float
     */
    public static function formatUnits(string $value, string $unit = 'ark'): float
    {
        switch (strtolower($unit)) {
            case 'wei':
                return (float) bcdiv($value, (string) self::WEI_MULTIPLIER, 18);
            case 'gwei':
                return (float) bcdiv($value, (string) self::GWEI_MULTIPLIER, 18);
            case 'ark':
                return (float) bcdiv($value, (string) self::ARK_MULTIPLIER, 18);
            default:
                throw new InvalidArgumentException("Unsupported unit: {$unit}. Supported units are 'wei', 'gwei', and 'ark'.");
        }
    }
}