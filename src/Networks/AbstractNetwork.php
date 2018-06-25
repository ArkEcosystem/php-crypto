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

namespace ArkEcosystem\Crypto\Networks;

/**
 * This is the abstract network class.
 *
 * @author Brian Faust <brian@ark.io>
 */
abstract class AbstractNetwork
{
    /**
     * Call a method on the network instance.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public static function __callStatic(string $method, array $args)
    {
        return static::getFactory()->{$method}(...$args);
    }

    /**
     * Create a new network instance.
     *
     * @return mixed
     */
    public static function create()
    {
        return new static();
    }

    /**
     * Get the version of the network.
     *
     * @return int
     */
    public static function getVersion(): int
    {
        $byte = static::getAddressByte();

        return hexdec("0x{$byte}");
    }

    /**
     * Get the byte representation of the wif prefix.
     *
     * @return int
     */
    public static function getWifByte(): string
    {
        return dechex(static::getWif());
    }
}
