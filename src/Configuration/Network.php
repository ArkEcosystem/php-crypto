<?php

declare(strict_types=1);



namespace ArkEcosystem\Crypto\Configuration;

use ArkEcosystem\Crypto\Networks\AbstractNetwork;
use ArkEcosystem\Crypto\Networks\Devnet;
use BitWasp\Bitcoin\Bitcoin;

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
     * @var AbstractNetwork
     */
    private static $network;

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
        return static::get()->{$method}(...$args);
    }

    /**
     * Get the network used for crypto operations.
     *
     * @return AbstractNetwork
     */
    public static function get(): AbstractNetwork
    {
        return static::$network ?? Devnet::new();
    }

    /**
     * Set the network used for crypto operations.
     *
     * @param AbstractNetwork $network
     */
    public static function set(AbstractNetwork $network): void
    {
        static::$network = $network;

        Bitcoin::setNetwork($network);
    }
}
