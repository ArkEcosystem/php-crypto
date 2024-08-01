<?php

declare(strict_types=1);



namespace ArkEcosystem\Crypto\Configuration;

use ArkEcosystem\Crypto\Enums\Fees;
use ArkEcosystem\Crypto\Enums\Types;
use PHPUnit\Util\Type;

/**
 * This is the fee configuration class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class Fee
{
    /**
     * Custom transaction fees.
     *
     * @var array
     */
    private static $customFees = [];

    /**
     * Get the transaction fee for the given type.
     *
     * @return string
     */
    public static function get(int $type): string
    {
        return isset(static::$customFees[$type]) ? static::$customFees[$type] : Types::fromValue($type)->defaultFee();
    }

    /**
     * Set the transaction fee for the given type.
     *
     * @param int    $type
     * @param string $fee
     */
    public static function set(int $type, string $fee): void
    {
        static::$customFees[$type] = $fee;
    }
}
