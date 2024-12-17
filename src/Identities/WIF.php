<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Identities;

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
        return PrivateKey::fromPassphrase($passphrase)->toWif();
    }
}
