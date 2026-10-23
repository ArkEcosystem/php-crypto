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

use ArkEcosystem\Crypto\Binary\Bytes;

/**
 * This is the hash class.
 */
class Hash
{
    /**
     * Hash the given data with SHA-256.
     *
     * @param Bytes|string $data
     *
     * @return Bytes
     */
    public static function sha256($data): Bytes
    {
        return new Bytes(hash('sha256', static::binary($data), true));
    }

    /**
     * Hash the given data with SHA-256 twice.
     *
     * @param Bytes|string $data
     *
     * @return Bytes
     */
    public static function sha256d($data): Bytes
    {
        return static::sha256(static::sha256($data));
    }

    /**
     * Hash the given data with RIPEMD-160.
     *
     * @param Bytes|string $data
     *
     * @return Bytes
     */
    public static function ripemd160($data): Bytes
    {
        return new Bytes(hash('ripemd160', static::binary($data), true));
    }

    /**
     * @param Bytes|string $data
     *
     * @return string
     */
    private static function binary($data): string
    {
        return $data instanceof Bytes ? $data->getBinary() : $data;
    }
}
