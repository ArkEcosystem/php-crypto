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
        if (null === $endianness) {
            return pack('S', $data);
        }
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
        if (null === $endianness) {
            return pack('L', $data);
        }
    }

    /**
     * Write an unsigned 64 bit integer.
     *
     * @param int   $data
     * @param mixed $endianness
     *
     * @return string
     */
    public static function bit64(int $data, $endianness = false): string
    {
        // big-endian
        if (true === $endianness) {
            return pack('J', $data);
        }

        // little-endian
        if (false === $endianness) {
            return pack('P', $data);
        }

        // machine byte order
        if (null === $endianness) {
            return pack('Q', $data);
        }
    }
}
