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

namespace ArkEcosystem\ArkCrypto\Utils;

use Exception;
use StephenHill\Base58 as B58;

class Base58
{
    /**
     * Encode a string using Base58.
     *
     * @param string $value
     *
     * @return string
     */
    public static function encode(string $value): string
    {
        return (new B58())->encode($value);
    }

    /**
     * Decode a Base58 encoded string.
     *
     * @param string $value
     *
     * @return string
     */
    public static function decode(string $value): string
    {
        return (new B58())->decode($value);
    }

    /**
     * Encode a string using Base58 with a 4 character checksum.
     *
     * @param string $value
     *
     * @return string
     */
    public static function encodeCheck(string $value): string
    {
        return static::encode($value.static::digest($value));
    }

    /**
     * Decode and verify the checksum of a Base58 encoded string.
     *
     * @param string $value
     *
     * @return string
     */
    public static function decodeCheck(string $value): string
    {
        $result = static::decode($value);

        $check  = substr($result, -4);
        $result = substr($result, 0, -4);

        if (static::digest($result) !== $check) {
            throw new Exception('Invalid checksum');
        }

        return $result;
    }

    /**
     * Create a digest for the given value.
     *
     * @param string $value
     *
     * @return string
     */
    private static function digest(string $value): string
    {
        $digest = hash('sha256', $value, true);
        $digest = hash('sha256', $digest, true);

        return substr($digest, 0, 4);
    }
}
