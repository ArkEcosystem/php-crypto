<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Networks;

use BitWasp\Bitcoin\Network\Network;

abstract class AbstractNetwork extends Network implements NetworkInterface
{
    /**
     * {@inheritdoc}
     *
     * @see Network::$base58PrefixMap
     */
    protected $base58PrefixMap = [
        self::BASE58_WIF => 'aa', // 170
    ];

    /**
     * Create a new network instance.
     *
     * @return mixed
     */
    public static function new()
    {
        return new static();
    }

    public function wif(): string
    {
        return $this->getPrivByte();
    }
}
