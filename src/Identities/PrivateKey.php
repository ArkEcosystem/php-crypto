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

use ArkEcosystem\Crypto\EcAdapter\Impl\PhpEcc\Adapter\EcAdapter;
use ArkEcosystem\Crypto\EcAdapter\Impl\PhpEcc\Key\PrivateKey as EcPrivateKey;
use ArkEcosystem\Crypto\Networks\AbstractNetwork;
use BitWasp\Bitcoin\Bitcoin;
use BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface;
use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\Secp256k1\Serializer\Key\PrivateKeySerializer;
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
    public static function ecAdapter(): EcAdapterInterface
    {
        return new EcAdapter(
            Bitcoin::getMath(),
            Bitcoin::getGenerator()
        );
    }

    /**
     * Derive the private key for the given passphrase.
     *
     * @param string $passphrase
     *
     * @return EcPrivateKey
     */
    public static function fromPassphrase(string $passphrase): EcPrivateKey
    {
        $passphrase = Hash::sha256(new Buffer($passphrase));

        return self::serializer()->parse(
            data: $passphrase,
            compressed: true
        );
    }

    /**
     * Create a private key instance from a hex string.
     *
     * @param \BitWasp\Buffertools\BufferInterface|string $privateKey
     *
     * @return EcPrivateKey
     */
    public static function fromHex($privateKey): EcPrivateKey
    {
        return self::serializer()->parse(
            data: Buffer::hex($privateKey),
            compressed: true
        );
    }

    /**
     * Derive the private key for the given WIF.
     *
     * @param string                                             $wif
     * @param AbstractNetwork|null $network
     *
     * @return EcPrivateKey
     */
    public static function fromWif(string $wif, AbstractNetwork $network = null): EcPrivateKey
    {
        return (new PrivateKeyFactory(
            self::ecAdapter()
        ))->fromWif($wif, $network);
    }

    private static function serializer(): PrivateKeySerializer
    {
        return new PrivateKeySerializer(self::ecAdapter());
    }
}
