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
     * @param bool|null $bigEndian
     *
     * @return string
     */
    public static function bit16(int $data, ?bool $bigEndian = false): string
    {
        // big-endian
        if ($bigEndian === true) {
            return pack('n', $data);
        }

        // little-endian
        if ($bigEndian === false) {
            return pack('v', $data);
        }

        // machine byte order
        return pack('S', $data);
    }

    /**
     * Write an unsigned 32 bit integer.
     *
     * @param int   $data
     * @param bool|null $bigEndian
     *
     * @return string
     */
    public static function bit32(int $data, ?bool $bigEndian = false): string
    {
        // big-endian
        if ($bigEndian === true) {
            return pack('N', $data);
        }

        // little-endian
        if ($bigEndian === false) {
            return pack('V', $data);
        }

        // machine byte order
        return pack('L', $data);
    }

    /**
     * Write an unsigned 64 bit integer.
     *
     * @param int   $data
     * @param bool $bigEndian
     *
     * @return string
     */
    public static function bit64(int|string $data, bool $bigEndian = false): string
    {
        $gmpValue = is_string($data) ? gmp_init($data, 10) : gmp_init($data);

        $hex   = str_pad(gmp_strval($gmpValue, 16), 16, '0', STR_PAD_LEFT);
        $bytes = hex2bin($hex);

        // Big-endian
        if ($bigEndian === true) {
            return $bytes;
        }

        // Convert to little-endian
        return strrev($bytes);
    }
}
