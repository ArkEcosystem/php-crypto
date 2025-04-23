<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Identities;

use BitWasp\Bitcoin\Bitcoin;
use BitWasp\Bitcoin\Crypto\EcAdapter\EcAdapterFactory;
use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PublicKey as EcPublicKey;
use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Signature\CompactSignatureInterface;
use BitWasp\Bitcoin\Key\Factory\PublicKeyFactory;
use BitWasp\Buffertools\BufferInterface;

class PublicKey
{
    public string $publicKey;

    public EcPublicKey $instance;

    public function __construct(EcPublicKey $instance)
    {
        $this->instance  = $instance;
        $this->publicKey = $instance->getHex();
    }

    /**
     * Derive the public from the given passphrase.
     *
     * @param string $passphrase
     *
     * @return PublicKey
     */
    public static function fromPassphrase(string $passphrase): self
    {
        return new self(PrivateKey::fromPassphrase($passphrase)->getPublicKey());
    }

    /**
     * Create a public key instance from a hex string.
     *
     * @param BufferInterface|string $publicKey
     *
     * @return PublicKey
     */
    public static function fromHex($publicKey): self
    {
        $instance = (new PublicKeyFactory(
            EcAdapterFactory::getPhpEcc(
                Bitcoin::getMath(),
                Bitcoin::getGenerator()
            )
        ))->fromHex($publicKey);

        return new self($instance);
    }

    /**
     * Create a public key instance from a binary string.
     *
     * @param BufferInterface|string $publicKey
     *
     * @return PublicKey
     */
    public static function recover(BufferInterface $message, CompactSignatureInterface $signature): self
    {
        $ecAdapter = EcAdapterFactory::getPhpEcc(
            Bitcoin::getMath(),
            Bitcoin::getGenerator()
        );

        $instance = $ecAdapter->recover($message, $signature);

        return new self($instance);
    }
}
