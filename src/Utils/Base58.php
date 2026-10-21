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

namespace ArkEcosystem\Crypto\Utils;

use InvalidArgumentException;

/**
 * Base58 and Base58Check encoding with the Bitcoin alphabet.
 */
class Base58
{
    private const ALPHABET = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';

    /**
     * Encode the given binary string.
     *
     * @param string $binary
     *
     * @return string
     */
    public static function encode(string $binary): string
    {
        if ($binary === '') {
            return '';
        }

        $number  = gmp_import($binary);
        $encoded = '';

        while (gmp_cmp($number, 0) > 0) {
            [$number, $remainder] = gmp_div_qr($number, 58);
            $encoded              = self::ALPHABET[gmp_intval($remainder)].$encoded;
        }

        $leadingZeros = strlen($binary) - strlen(ltrim($binary, "\0"));

        return str_repeat(self::ALPHABET[0], $leadingZeros).$encoded;
    }

    /**
     * Decode the given Base58 string into binary.
     *
     * @param string $encoded
     *
     * @return string
     */
    public static function decode(string $encoded): string
    {
        if ($encoded === '') {
            return '';
        }

        $number = gmp_init(0);

        foreach (str_split($encoded) as $character) {
            $position = strpos(self::ALPHABET, $character);

            if ($position === false) {
                throw new InvalidArgumentException('The given value contains invalid Base58 characters.');
            }

            $number = gmp_add(gmp_mul($number, 58), $position);
        }

        $decoded      = gmp_cmp($number, 0) > 0 ? gmp_export($number) : '';
        $leadingZeros = strlen($encoded) - strlen(ltrim($encoded, self::ALPHABET[0]));

        return str_repeat("\0", $leadingZeros).$decoded;
    }

    /**
     * Encode the given binary string with a 4-byte double SHA-256 checksum.
     *
     * @param string $binary
     *
     * @return string
     */
    public static function encodeCheck(string $binary): string
    {
        return static::encode($binary.static::checksum($binary));
    }

    /**
     * Decode the given Base58Check string and verify its checksum.
     *
     * @param string $encoded
     *
     * @return string
     */
    public static function decodeCheck(string $encoded): string
    {
        $decoded = static::decode($encoded);

        if (strlen($decoded) < 4) {
            throw new InvalidArgumentException('The given value is too short to contain a checksum.');
        }

        $payload  = substr($decoded, 0, -4);
        $checksum = substr($decoded, -4);

        if (! hash_equals(static::checksum($payload), $checksum)) {
            throw new InvalidArgumentException('The given value has an invalid checksum.');
        }

        return $payload;
    }

    /**
     * @param string $binary
     *
     * @return string
     */
    private static function checksum(string $binary): string
    {
        return substr(Hash::sha256d($binary)->getBinary(), 0, 4);
    }
}
