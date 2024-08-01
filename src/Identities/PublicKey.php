<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Identities;

use BitWasp\Bitcoin\Bitcoin;
use BitWasp\Bitcoin\Crypto\EcAdapter\EcAdapterFactory;
use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PublicKey as EcPublicKey;
use BitWasp\Bitcoin\Key\Factory\PublicKeyFactory;
use Elliptic\EC;

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
        return PrivateKey::fromPassphrase($passphrase)->getPublicKey();
    }

    /**
     * Create a public key instance from a multi-signature asset.
     *
     * @param int   $min
     * @param array $publicKeys
     *
     * @return EcPublicKey
     */
    public static function fromMultiSignatureAsset(int $min, array $publicKeys): EcPublicKey
    {
        $minKey = static::fromPassphrase('0'.dechex($min));
        $keys   = [$minKey->getHex(), ...$publicKeys];

        $curve = (new EC('secp256k1'))->curve;
        $P     = $curve->jpoint(null, null, null);

        foreach ($keys as $publicKey) {
            $P = $P->add($curve->decodePoint($publicKey, 'hex'));
        }

        return static::fromHex(bin2hex(implode(array_map('chr', $P->encodeCompressed(true)))));
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
