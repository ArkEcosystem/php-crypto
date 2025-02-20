<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Networks;

class Mainnet extends AbstractNetwork
{
    /**
     * {@inheritdoc}
     *
     * @see Network::$base58PrefixMap
     */
    protected $base58PrefixMap = [
        self::BASE58_WIF => 'ba', // 186
    ];

    /**
     * Get the chain identifier.
     *
     * @return int
     */
    public function chainId(): int
    {
        return 10000;
    }

    /**
     * Get the network epoch.
     *
     * @return string
     */
    public function epoch(): string
    {
        return '2017-03-21T13:00:00.000Z';
    }
}
