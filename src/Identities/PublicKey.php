<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Identities;

use BitWasp\Bitcoin\Bitcoin;
use BitWasp\Bitcoin\Crypto\EcAdapter\EcAdapterFactory;
use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PublicKey as EcPublicKey;
use BitWasp\Bitcoin\Key\Factory\PublicKeyFactory;

class PublicKey
{
    public EcPublicKey $publicKey;

    public function __construct(EcPublicKey $publicKey)
    {
        $this->publicKey = $publicKey;
    }

    /**
     * Get the public key in hex format.
     *
     * @return string
     */
    public function getHex(): string
    {
        return $this->publicKey->getHex();
    }

    /**
     * Derive the public from the given passphrase.
     *
     * @param string $passphrase
     *
     * @return self
     */
    public static function fromPassphrase(string $passphrase): self
    {
        return new static(PrivateKey::fromPassphrase($passphrase)->instance->getPublicKey());
    }

    /**
     * Create a public key instance from a hex string.
     *
     * @param \BitWasp\Buffertools\BufferInterface|string $publicKey
     *
     * @return self
     */
    public static function fromHex($publicKey): self
    {
        $factory = (new PublicKeyFactory(
            EcAdapterFactory::getPhpEcc(
                Bitcoin::getMath(),
                Bitcoin::getGenerator()
            )
        ));

        return new static($factory->fromHex($publicKey));
    }
}
