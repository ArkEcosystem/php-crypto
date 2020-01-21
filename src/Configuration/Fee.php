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
        Types::TRANSFER                      => Fees::TRANSFER,
        Types::SECOND_SIGNATURE_REGISTRATION => Fees::SECOND_SIGNATURE_REGISTRATION,
        Types::DELEGATE_REGISTRATION         => Fees::DELEGATE_REGISTRATION,
        Types::VOTE                          => Fees::VOTE,
        Types::MULTI_SIGNATURE_REGISTRATION  => Fees::MULTI_SIGNATURE_REGISTRATION,
        Types::IPFS                          => Fees::IPFS,
        Types::MULTI_PAYMENT                 => Fees::MULTI_PAYMENT,
        Types::DELEGATE_RESIGNATION          => Fees::DELEGATE_RESIGNATION,
        Types::HTLC_LOCK                     => Fees::HTLC_LOCK,
        Types::HTLC_CLAIM                    => Fees::HTLC_CLAIM,
        Types::HTLC_REFUND                   => Fees::HTLC_REFUND,
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
