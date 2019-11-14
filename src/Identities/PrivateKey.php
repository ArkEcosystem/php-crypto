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

use ArkEcosystem\Crypto\Networks\AbstractNetwork;
use BitWasp\Bitcoin\Bitcoin;
use BitWasp\Bitcoin\Crypto\EcAdapter\EcAdapterFactory;
use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PrivateKey as EcPrivateKey;
use BitWasp\Bitcoin\Crypto\Hash;
use BitWasp\Bitcoin\Key\Factory\PrivateKeyFactory;
use BitWasp\Buffertools\Buffer;

/**
 * This is the private key class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class PrivateKey
{
    /**
     * Derive the private key for the given passphrase.
     *
     * @param string $passphrase
     *
     * @return \BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PrivateKey
     */
    public static function fromPassphrase(string $passphrase): EcPrivateKey
    {
        $passphrase = Hash::sha256(new Buffer($passphrase));

        return (new PrivateKeyFactory(
            EcAdapterFactory::getPhpEcc(
                Bitcoin::getMath(),
                Bitcoin::getGenerator()
            )
        ))->fromHexCompressed($passphrase->getHex());
    }

    /**
     * Create a private key instance from a hex string.
     *
     * @param \BitWasp\Buffertools\BufferInterface|string $privateKey
     *
     * @return \BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PrivateKey
     */
    public static function fromHex($privateKey): EcPrivateKey
    {
        return (new PrivateKeyFactory(
            EcAdapterFactory::getPhpEcc(
                Bitcoin::getMath(),
                Bitcoin::getGenerator()
            )
        ))->fromHexCompressed($privateKey);
    }

    /**
     * Derive the private key for the given WIF.
     *
     * @param string                                             $wif
     * @param \ArkEcosystem\Crypto\Networks\AbstractNetwork|null $network
     *
     * @return \BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PrivateKey
     */
    public static function fromWif(string $wif, AbstractNetwork $network = null): EcPrivateKey
    {
        return (new PrivateKeyFactory(
            EcAdapterFactory::getPhpEcc(
                Bitcoin::getMath(),
                Bitcoin::getGenerator()
            )
        ))->fromWif($wif, $network);
    }
}
