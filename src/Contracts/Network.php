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

namespace ArkEcosystem\Crypto\Contracts;

use BitWasp\Bitcoin\Network\Network as TokenNetwork;

/**
 * This is the network contracts class.
 *
 * @author Brian Faust <brian@ark.io>
 */
interface Network
{
    /**
     * Create a new network instance.
     *
     * @return mixed
     */
    public static function new();

    /**
     * Get the epoch of the network.
     *
     * @return int
     */
    public static function getEpoch(): string;

    /**
     * Get the version of the network.
     *
     * @return int
     */
    public static function getVersion(): int;

    /**
     * Get the nethash of the network.
     *
     * @return string
     */
    public static function getNethash(): string;

    /**
     * Get the wif prefix of the network.
     *
     * @return int
     */
    public static function getWif(): int;

    /**
     * Get the byte representation of the wif prefix.
     *
     * @return int
     */
    public static function getWifByte(): string;

    /**
     * Get a network instance.
     *
     * @return \BitWasp\Bitcoin\Network\Network
     */
    public static function getFactory(): TokenNetwork;
}
