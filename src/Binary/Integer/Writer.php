<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Binary\Integer;

/**
 * This is the integer writer class.
 */
class Writer
{
    /**
     * Write a signed 8 bit integer.
     *
     * @param string $data
     *
     * @return string
     */
    public static function bit8(int $data): string
    {
        return pack('c', $data);
    }

    /**
     * Write a signed 16 bit integer.
     *
     * @param string $data
     *
     * @return string
     */
    public static function bit16(int $data): string
    {
        return pack('s', $data);
    }

    /**
     * Write a signed 32 bit integer.
     *
     * @param string $data
     *
     * @return string
     */
    public static function bit32(int $data): string
    {
        return pack('l', $data);
    }

    /**
     * Write a signed 64 bit integer.
     *
     * @param string $data
     *
     * @return string
     */
    public static function bit64(int $data): string
    {
        return pack('q', $data);
    }
}
