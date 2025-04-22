<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Identities;

use BitWasp\Bitcoin\Bitcoin;
use BitWasp\Bitcoin\Crypto\EcAdapter\EcAdapterFactory;
use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PublicKey as EcPublicKey;
use BitWasp\Bitcoin\Key\Factory\PublicKeyFactory;

class PublicKey
{
    /**
     * Derive the public from the given passphrase.
     *
     * @param string $passphrase
     *
     * @return EcPublicKey
     */
    public static function fromPassphrase(string $passphrase): EcPublicKey
    {
        return PrivateKey::fromPassphrase($passphrase)->instance->getPublicKey();
    }

    /**
     * Create a public key instance from a hex string.
     *
     * @param \BitWasp\Buffertools\BufferInterface|string $publicKey
     *
     * @return EcPublicKey
     */
    public static function fromHex($publicKey): EcPublicKey
    {
        return (new PublicKeyFactory(
            EcAdapterFactory::getPhpEcc(
                Bitcoin::getMath(),
                Bitcoin::getGenerator()
            )
        ))->fromHex($publicKey);
    }
}
