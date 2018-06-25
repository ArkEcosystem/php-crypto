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

namespace ArkEcosystem\Crypto\Identity;

use BitWasp\Bitcoin\Address\PayToPubKeyHashAddress;
use BitWasp\Bitcoin\Base58;
use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PrivateKey as EccPrivateKey;
use BitWasp\Bitcoin\Crypto\Hash;
use BitWasp\Bitcoin\Network\NetworkInterface;
use BitWasp\Buffertools\Buffer;
use BrianFaust\Binary\UnsignedInteger\Reader;
use BrianFaust\Binary\UnsignedInteger\Writer;

/**
 * This is the address class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class Address
{
    /**
     * Derive the address from the given public key.
     *
     * @param string $publicKey
     * @param int    $version
     *
     * @return string
     */
    public static function fromPublicKey(string $publicKey, int $version = 0x17): string
    {
        $ripemd160 = Hash::ripemd160(new Buffer(hex2bin($publicKey)));
        $seed      = Writer::bit8($version).$ripemd160->getBinary();

        return Base58::encodeCheck(new Buffer($seed));
    }

    /**
     * Derive the address from the given secret.
     *
     * @param string                                         $secret
     * @param \BitWasp\Bitcoin\Network\NetworkInterface|null $network
     *
     * @return string
     */
    public static function fromSecret(string $secret, NetworkInterface $network = null): string
    {
        return static::fromPrivateKey(PrivateKey::fromSecret($secret), $network);
    }

    /**
     * Derive the address from the given private key.
     *
     * @param \BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PrivateKey $privateKey
     * @param \BitWasp\Bitcoin\Network\NetworkInterface|null               $network
     *
     * @return string
     */
    public static function fromPrivateKey(EccPrivateKey $privateKey, NetworkInterface $network = null): string
    {
        $publicKey = $privateKey->getPublicKey();
        $digest    = Hash::ripemd160(new Buffer($publicKey->getBinary()));

        return (new PayToPubKeyHashAddress($digest))->getAddress($network);
    }

    /**
     * Validate the given address.
     *
     * @param string $address
     * @param string $networkVersion
     *
     * @return bool
     */
    public static function validate(string $address, int $version = 0x17): bool
    {
        $address = Base58::decodeCheck($address);

        return Reader::bit8($address->getBinary()) === $version;
    }
}
