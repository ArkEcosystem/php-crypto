<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Networks;

class Devnet extends AbstractNetwork
{
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
