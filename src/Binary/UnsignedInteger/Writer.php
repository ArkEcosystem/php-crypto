<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Binary\UnsignedInteger;

/**
 * This is the unsigned integer writer class.
 */
class Writer
{
    /**
     * Write an unsigned 8 bit integer.
     *
     * @param int $data
     *
     * @return string
     */
    public static function bit8(int $data): string
    {
        return pack('C', $data);
    }

    /**
     * Write an unsigned 16 bit integer.
     *
     * @param int   $data
     * @param mixed $endianness
     *
     * @return string
     */
    public static function bit16(int $data, $endianness = false): string
    {
        // big-endian
        if (true === $endianness) {
            return pack('n', $data);
        }

        // little-endian
        if (false === $endianness) {
            return pack('v', $data);
        }

        // machine byte order
        return pack('S', $data);
    }

    /**
     * Write an unsigned 32 bit integer.
     *
     * @param int   $data
     * @param mixed $endianness
     *
     * @return string
     */
    public static function bit32(int $data, $endianness = false): string
    {
        // big-endian
        if (true === $endianness) {
            return pack('N', $data);
        }

        // little-endian
        if (false === $endianness) {
            return pack('V', $data);
        }

        // machine byte order
        return pack('L', $data);
    }

    /**
     * Write an unsigned 64 bit integer.
     *
     * @param int   $data
     * @param mixed $endianness
     *
     * @return string
     */
    public static function bit64(int|string $data, $endianness = false): string
    {
        $gmpValue = is_string($data) ? gmp_init($data, 10) : gmp_init($data);

        $hex   = str_pad(gmp_strval($gmpValue, 16), 16, '0', STR_PAD_LEFT);
        $bytes = hex2bin($hex);

        // Default to little-endian (reverse the big-endian hex representation)
        if (false === $endianness) {
            return strrev($bytes); // Convert to little-endian
        }

        // Big-endian
        if (true === $endianness) {
            return $bytes;
        }

        // Machine order
        return pack('Q', gmp_cmp($gmpValue, PHP_INT_MAX) <= 0 ? gmp_intval($gmpValue) : 0);
    }
}
