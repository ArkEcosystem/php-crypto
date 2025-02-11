<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Identities;

use ArkEcosystem\Crypto\Configuration\Network;

class WIF
{
    /**
     * Derive the WIF from the given passphrase.
     *
     * @param string $passphrase
     *
     * @return string
     */
    public static function fromPassphrase(string $passphrase): string
    {
        return PrivateKey::fromPassphrase($passphrase)->toWif(Network::get());
    }
}
