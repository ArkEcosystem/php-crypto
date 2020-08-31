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

use BitWasp\Bitcoin\Network\Network;
use BitWasp\Bitcoin\Script\ScriptType;

/**
 * This is the abstract network class.
 *
 * @author Brian Faust <brian@ark.io>
 */
abstract class AbstractNetwork extends Network
{
    /**
     * {@inheritdoc}
     */
    protected $bip32ScriptTypeMap = [
        self::BIP32_PREFIX_XPUB => ScriptType::P2PKH,
        self::BIP32_PREFIX_XPRV => ScriptType::P2PKH,
    ];

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
     * Get the network version as number.
     *
     * @return int
     */
    public function version(): int
    {
        return hexdec($this->getAddressByte());
    }

    abstract public function pubKeyHash(): int;

    abstract public function epoch(): string;
}
