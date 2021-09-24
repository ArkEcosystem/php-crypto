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

namespace ArkEcosystem\Crypto\Binary\Integer;

/**
 * This is the integer reader class.
 */
class Reader
{
    /**
     * Read a signed 8 bit integer.
     *
     * @param string $data
     * @param int    $offset
     *
     * @return int
     */
    public static function bit8(string $data, int $offset = 0): int
    {
        return unpack('c', $data, $offset)[1];
    }

    /**
     * Read a signed 16 bit integer.
     *
     * @param string $data
     * @param int    $offset
     *
     * @return int
     */
    public static function bit16(string $data, int $offset = 0): int
    {
        return unpack('s', $data, $offset)[1];
    }

    /**
     * Read a signed 32 bit integer.
     *
     * @param string $data
     * @param int    $offset
     *
     * @return int
     */
    public static function bit32(string $data, int $offset = 0): int
    {
        return unpack('l', $data, $offset)[1];
    }

    /**
     * Read a signed 64 bit integer.
     *
     * @param string $data
     * @param int    $offset
     *
     * @return int
     */
    public static function bit64(string $data, int $offset = 0): int
    {
        return unpack('q', $data, $offset)[1];
    }
}
