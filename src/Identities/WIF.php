<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Identities;

use ArkEcosystem\Crypto\Networks\AbstractNetwork;

class WIF
{
    /**
     * Derive the WIF from the given passphrase.
     *
     * @param string                                             $passphrase
     * @param AbstractNetwork|null $network
     *
     * @return string
     */
    public static function fromPassphrase(string $passphrase, AbstractNetwork $network = null): string
    {
        return PrivateKey::fromPassphrase($passphrase)->toWif($network);
    }
}
