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
     * @param mixed  $endianness
     *
     * @return int
     */
    public static function bit16(string $data, int $offset = 0, $endianness = false): int
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
        if (null === $endianness) {
            return unpack('S', $data, $offset)[1];
        }
    }

    /**
     * Read an unsigned 32 bit integer.
     *
     * @param string $data
     * @param int    $offset
     * @param mixed  $endianness
     *
     * @return int
     */
    public static function bit32(string $data, int $offset = 0, $endianness = false): int
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
        if (null === $endianness) {
            return unpack('L', $data, $offset)[1];
        }
    }

    /**
     * Read an unsigned 64 bit integer.
     *
     * @param string $data
     * @param int    $offset
     * @param mixed  $endianness
     *
     * @return int
     */
    public static function bit64(string $data, int $offset = 0, bool $endianness = false): int
    {
        // big-endian
        if (true === $endianness) {
            return unpack('J', $data, $offset)[1];
        }

        // little-endian
        if (false === $endianness) {
            return unpack('P', $data, $offset)[1];
        }

        // machine byte order
        if (null === $endianness) {
            return unpack('Q', $data, $offset)[1];
        }
    }
}
