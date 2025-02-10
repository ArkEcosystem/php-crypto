<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Networks;

abstract class AbstractNetwork
{
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
