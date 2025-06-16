<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Binary\UnsignedInteger;

/**
 * This is the unsigned integer reader class.
 */
class Reader
{
    /**
     * Read an unsigned 8 bit integer.
     *
     * @param string $data
     * @param int    $offset
     *
     * @return int
     */
    public static function bit8(string $data, int $offset = 0): int
    {
        return unpack('C', $data, $offset)[1];
    }

    /**
     * Read an unsigned 16 bit integer.
     *
     * @param string $data
     * @param int    $offset
     * @param bool|null  $endianness
     *
     * @return int
     */
    public static function bit16(string $data, int $offset = 0, ?bool $endianness = false): int
    {
        // big-endian
        if (true === $endianness) {
            return unpack('n', $data, $offset)[1];
        }

        // little-endian
        if (false === $endianness) {
            return unpack('v', $data, $offset)[1];
        }

        // machine byte order
        return unpack('S', $data, $offset)[1];
    }

    /**
     * Read an unsigned 32 bit integer.
     *
     * @param string $data
     * @param int    $offset
     * @param bool|null  $endianness
     *
     * @return int
     */
    public static function bit32(string $data, int $offset = 0, ?bool $endianness = false): int
    {
        // big-endian
        if (true === $endianness) {
            return unpack('N', $data, $offset)[1];
        }

        // little-endian
        if (false === $endianness) {
            return unpack('V', $data, $offset)[1];
        }

        // machine byte order
        return unpack('L', $data, $offset)[1];
    }

    /**
     * Read an unsigned 64 bit integer.
     *
     * @param string $data
     * @param int    $offset
     * @param bool   $endianness
     *
     * @return int
     */
    public static function bit64(string $data, int $offset = 0, bool $endianness = false): int|string
    {
        $bytes = substr($data, $offset, 8);

        if ($endianness === false) {
            // big-endian - reverse for little-endian system
            $bytes = strrev($bytes);
        }

        $hex      = bin2hex($bytes);
        $gmpValue = gmp_init($hex, 16);

        // If it fits in PHP's int range, return as int
        if (gmp_cmp($gmpValue, PHP_INT_MAX) <= 0) {
            return gmp_intval($gmpValue);
        }

        // Otherwise return as string
        return gmp_strval($gmpValue);
    }
}
