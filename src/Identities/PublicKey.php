<?php

declare(strict_types=1);

/*
 * This file is part of Ark PHP Crypto.
 *
 * (c) Ark Ecosystem <info@ark.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ArkEcosystem\Crypto\Identities;

use BitWasp\Bitcoin\Bitcoin;
use BitWasp\Bitcoin\Crypto\EcAdapter\EcAdapterFactory;
use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PublicKey as EcPublicKey;
use BitWasp\Bitcoin\Key\Factory\PublicKeyFactory;
use Elliptic\EC;

/**
 * This is the public key class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class PublicKey
{
    /**
     * Derive the public from the given passphrase.
     *
     * @param string $passphrase
     *
     * @return \BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PublicKey
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
     * @return \BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PublicKey
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
     * @return \BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PublicKey
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
