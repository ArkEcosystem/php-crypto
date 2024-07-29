<?php

declare(strict_types=1);

/*
 * This file is part of Ark PHP Crypto.
 *
 * (c) Ark Ecosystem <info@ark.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ArkEcosystem\Crypto\Configuration;

use ArkEcosystem\Crypto\Enums\Fees;
use ArkEcosystem\Crypto\Enums\Types;

/**
 * This is the fee configuration class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class Fee
{
    /**
     * The default transaction fees.
     *
     * @var array
     */
    private static $fees = [
        Types::TRANSFER->value                      => Fees::TRANSFER,
        Types::VALIDATOR_REGISTRATION->value        => Fees::VALIDATOR_REGISTRATION,
        Types::VOTE->value                          => Fees::VOTE,
        Types::MULTI_SIGNATURE_REGISTRATION->value  => Fees::MULTI_SIGNATURE_REGISTRATION,
        Types::MULTI_PAYMENT->value                 => Fees::MULTI_PAYMENT,
        Types::VALIDATOR_RESIGNATION->value         => Fees::VALIDATOR_RESIGNATION,
        Types::USERNAME_REGISTRATION->value         => Fees::USERNAME_REGISTRATION,
        Types::USERNAME_RESIGNATION->value          => Fees::USERNAME_RESIGNATION,
    ];

    /**
     * Get the transaction fee for the given type.
     *
     * @return string
     */
    public static function get(int $type): string
    {
        return static::$fees[$type];
    }

    /**
     * Set the transaction fee for the given type.
     *
     * @param int    $type
     * @param string $fee
     */
    public static function set(int $type, string $fee): void
    {
        static::$fees[$type] = $fee;
    }
}
