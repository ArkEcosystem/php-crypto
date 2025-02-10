<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Networks;

use BitWasp\Bitcoin\Network\Network;

abstract class AbstractNetwork extends Network
{
    /**
     * {@inheritdoc}
     *
     * @see Network::$base58PrefixMap
     */
    protected $base58PrefixMap = [
        self::BASE58_WIF           => 'aa',
    ];

    /**
     * {@inheritdoc}
     */
    public static function __callStatic(string $method, array $args)
    {
        return static::factory()->{$method}(...$args);
    }

    /**
     * Create a new network instance.
     *
     * @return mixed
     */
    public static function new()
    {
        return new static();
    }

    /**
     * Get the chain identifier.
     *
     * @return int
     */
    abstract public function chainId(): int;

    /**
     * Get the network epoch.
     *
     * @return string
     */
    abstract public function epoch(): string;
}
