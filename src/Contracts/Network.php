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
    public static function epoch(): string;

    /**
     * Get the version of the network.
     *
     * @return int
     */
    public static function version(): int;

    /**
     * Get the nethash of the network.
     *
     * @return string
     */
    public static function nethash(): string;

    /**
     * Get the wif prefix of the network.
     *
     * @return int
     */
    public static function wif(): int;

    /**
     * Get the byte representation of the wif prefix.
     *
     * @return int
     */
    public static function wifByte(): string;

    /**
     * Get a network factory.
     *
     * @return \BitWasp\Bitcoin\Network\Network
     */
    public static function factory(): TokenNetwork;
}
