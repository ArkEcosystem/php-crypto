<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Networks;

interface NetworkInterface
{
    /**
     * Get the chain identifier.
     *
     * @return int
     */
    public function chainId(): int;

    /**
     * Get the network epoch.
     *
     * @return string
     */
    public function epoch(): string;

    /**
     * Get the network WIF prefix.
     *
     * @return string
     */
    public function wif(): string;
}
