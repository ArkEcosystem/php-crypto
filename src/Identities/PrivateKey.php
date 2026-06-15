<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Identities;

use ArkEcosystem\Crypto\Configuration\Network;
use ArkEcosystem\Crypto\Helpers;
use BitWasp\Bitcoin\Bitcoin;
use BitWasp\Bitcoin\Crypto\EcAdapter\EcAdapterFactory;
use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Signature\CompactSignature;
use BitWasp\Bitcoin\Crypto\EcAdapter\Key\PrivateKeyInterface;
use BitWasp\Bitcoin\Crypto\Hash;
use BitWasp\Bitcoin\Key\Factory\PrivateKeyFactory;
use BitWasp\Buffertools\Buffer;
use BitWasp\Buffertools\BufferInterface;

class PrivateKey
{
    public PrivateKeyInterface $instance;

    public string $publicKey;

    public function __construct(PrivateKeyInterface $privateKey)
    {
        $this->instance  = $privateKey;
        $this->publicKey = $privateKey->getPublicKey()->getHex();
    }

    /**
     * Get the private key in hex format.
     *
     * @return string
     */
    public function getHex(): string
    {
        return $this->instance->getHex();
    }

    /**
     * Derive the private key for the given passphrase.
     *
     * @param string $passphrase
     *
     * @return self
     */
    public static function fromPassphrase(string $passphrase): self
    {
        $passphrase = Hash::sha256(new Buffer($passphrase));

        return new self(static::factory()->fromHexCompressed($passphrase->getHex()));
    }

    /**
     * Create a private key instance from a hex string.
     *
     * @param BufferInterface|string $privateKey
     *
     * @return self
     */
    public static function fromHex($privateKey): self
    {
        return new self(static::factory()->fromHexCompressed($privateKey));
    }

    /**
     * Derive the private key for the given WIF.
     *
     * @param string $wif
     *
     * @return self
     */
    public static function fromWif(string $wif): self
    {
        return new self(static::factory()->fromWif($wif, Network::get()));
    }

    /**
     * Sign a message using the private key.
     *
     * @param BufferInterface $message
     *
     * @return CompactSignature
     */
    public function sign(BufferInterface $message): CompactSignature
    {
        return $this->instance->signCompact($message);
    }

    /**
     * Sign a message using the private key and return an ECDSA signature.
     *
     * @param BufferInterface $message
     *
     * @return string
     */
    public function signToEcdsa(BufferInterface $message): string
    {
        $signature = $this->instance->signCompact($message);

        return sprintf(
            '%s%s%s',
            Helpers::gmpToHex($signature->getR()),
            Helpers::gmpToHex($signature->getS()),
            str_pad((string) $signature->getRecoveryId(), 2, '0', STR_PAD_LEFT),
        );
    }

    private static function factory(): PrivateKeyFactory
    {
        return new PrivateKeyFactory(
            EcAdapterFactory::getPhpEcc(
                Bitcoin::getMath(),
                Bitcoin::getGenerator()
            )
        );
    }
}
