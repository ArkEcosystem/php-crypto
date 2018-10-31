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

use BitWasp\Bitcoin\Key\PublicKeyFactory;
use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PublicKey as EcPublicKey;

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
     * Create a public key instance from a hex string.
     *
     * @param \BitWasp\Buffertools\BufferInterface|string $publicKey
     *
     * @return \BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PublicKey
     */
    public static function fromHex($publicKey): EcPublicKey
    {
        return PublicKeyFactory::fromHex($publicKey);
    }

    /**
     * Validate the given public key.
     *
     * @param \BitWasp\Buffertools\BufferInterface|string $publicKey
     *
     * @return bool
     */
    public static function validate(string $publicKey): bool
    {
        return PublicKeyFactory::validateHex($publicKey);
    }
}
