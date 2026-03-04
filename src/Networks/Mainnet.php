<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Networks;

class Mainnet extends AbstractNetwork implements NetworkInterface
{
    /**
     * {@inheritdoc}
     *
     * @see Network::$base58PrefixMap
     */
    protected $base58PrefixMap = [
        self::BASE58_WIF => 'ba', // 186
    ];

    public function chainId(): int
    {
        return 11811;
    }

    public function epoch(): string
    {
        return '2017-03-21T13:00:00.000Z';
    }
}
