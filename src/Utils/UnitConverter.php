<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Utils;

use Brick\Math\BigDecimal;
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
     * @return BigDecimal
     */
    public static function parseUnits($value, string $unit = 'ark'): BigDecimal
    {
        switch (strtolower($unit)) {
            case 'wei':
                return BigDecimal::of(bcmul((string) $value, (string) self::WEI_MULTIPLIER, 0));
            case 'gwei':
                return BigDecimal::of(bcmul((string) $value, (string) self::GWEI_MULTIPLIER, 0));
            case 'ark':
                return BigDecimal::of(bcmul((string) $value, (string) self::ARK_MULTIPLIER, 0));
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

    /**
     * Convert wei to ARK.
     *
     * @param string|int|float $value
     * @param string|null $suffix
     * @return string
     */
    public static function weiToArk(string | int | float $value, ?string $suffix = null): string
    {
        $convertedValue = (string) BigDecimal::of(self::formatUnits((string) self::parseUnits($value, 'wei'), 'ark'))
            ->stripTrailingZeros();

        if ($suffix !== null) {
            return $convertedValue.' '.$suffix;
        }

        return $convertedValue;
    }

    /**
     * Convert gwei to ARK.
     *
     * @param string|int|float $value
     * @param string|null $suffix
     * @return string
     */
    public static function gweiToArk(string | int | float $value, ?string $suffix = null): string
    {
        $convertedValue = (string) BigDecimal::of(self::formatUnits((string) self::parseUnits($value, 'gwei'), 'ark'))
            ->stripTrailingZeros();

        if ($suffix !== null) {
            return $convertedValue.' '.$suffix;
        }

        return $convertedValue;
    }
}
