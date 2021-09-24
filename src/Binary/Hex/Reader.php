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

namespace ArkEcosystem\Crypto\Binary\Hex;

/**
 * This is the hex reader class.
 */
class Reader
{
    /**
     * Read a hex string with low nibble first.
     *
     * @param string $data
     * @param int    $offset
     * @param mixed  $nibble
     *
     * @return string
     */
    public static function low(string $data, int $offset = 0, $nibble = null): string
    {
        return unpack($nibble ? "h{$nibble}" : 'h', $data, $offset)[1];
    }

    /**
     * Read a hex string with high nibble first.
     *
     * @param string $data
     * @param int    $offset
     * @param mixed  $nibble
     *
     * @return string
     */
    public static function high(string $data, int $offset = 0, $nibble = null): string
    {
        return unpack($nibble ? "H{$nibble}" : 'H', $data, $offset)[1];
    }
}
