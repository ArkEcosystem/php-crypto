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

namespace ArkEcosystem\Crypto\Configuration;

use ArkEcosystem\Crypto\Contracts\Network as AbstractNetwork;
use ArkEcosystem\Crypto\Networks\Mainnet;

/**
 * This is the network configuration class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class Network
{
    /**
     * The network used for crypto operations.
     *
     * @var \ArkEcosystem\Crypto\Contracts\Network
     */
    private static $network;

    /**
     * Get the network used for crypto operations.
     *
     * @return \ArkEcosystem\Crypto\Contracts\Network
     */
    public static function get(): AbstractNetwork
    {
        return static::$network ?? Mainnet::create();
    }

    /**
     * Set the network used for crypto operations.
     *
     * @param \ArkEcosystem\Crypto\Contracts\Network $network
     */
    public static function set(AbstractNetwork $network): void
    {
        static::$network = $network;
    }
}
