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
 * This is the hex writer class.
 */
class Writer
{
    /**
     * Write a hex string with low nibble first.
     *
     * @param mixed $data
     * @param mixed $nibble
     *
     * @return string
     */
    public static function low($data, $nibble = null): string
    {
        return pack($nibble ? "h{$nibble}" : 'h', $data);
    }

    /**
     * Write a hex string with high nibble first.
     *
     * @param string $data
     * @param mixed  $nibble
     *
     * @return string
     */
    public static function high($data, $nibble = null): string
    {
        return pack($nibble ? "H{$nibble}" : 'H', $data);
    }
}
