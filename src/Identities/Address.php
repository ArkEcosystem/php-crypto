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

use ArkEcosystem\Crypto\Configuration\Network as NetworkConfiguration;
use ArkEcosystem\Crypto\Helpers;
use ArkEcosystem\Crypto\Networks\AbstractNetwork;
use BitWasp\Bitcoin\Address\AddressCreator;
use BitWasp\Bitcoin\Address\PayToPubKeyHashAddress;
use BitWasp\Bitcoin\Base58;
use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PrivateKey as EccPrivateKey;
use BitWasp\Bitcoin\Crypto\Hash;
use BitWasp\Buffertools\Buffer;
use Konceiver\Binary\UnsignedInteger\Writer;

/**
 * This is the address class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class Address
{
    /**
     * Derive the address from the given passphrase.
     *
     * @param string               $passphrase
     * @param AbstractNetwork|null $network
     *
     * @return string
     */
    public static function fromPassphrase(string $passphrase, AbstractNetwork $network = null): string
    {
        return static::fromPrivateKey(PrivateKey::fromPassphrase($passphrase), $network);
    }

    /**
     * Derive the address from the given multi-signature asset.
     *
     * @param int   $min
     * @param array $publicKeys
     *
     * @return string
     */
    public static function fromMultiSignatureAsset(int $min, array $publicKeys): string
    {
        return static::fromPublicKey(PublicKey::fromMultiSignatureAsset($min, $publicKeys)->getHex());
    }

    /**
     * Derive the address from the given public key.
     *
     * @param string               $publicKey
     * @param AbstractNetwork|null $network
     *
     * @return string
     */
    public static function fromPublicKey(string $publicKey, $network = null): string
    {
        $network = $network ?? NetworkConfiguration::get();

        $ripemd160 = Hash::ripemd160(PublicKey::fromHex($publicKey)->getBuffer());
        $seed      = Writer::bit8(Helpers::version($network)).$ripemd160->getBinary();

        return Base58::encodeCheck(new Buffer($seed));
    }

    /**
     * Derive the address from the given private key.
     *
     * @param \BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PrivateKey $privateKey
     * @param AbstractNetwork|null                                         $network
     *
     * @return string
     */
    public static function fromPrivateKey(EccPrivateKey $privateKey, AbstractNetwork $network = null): string
    {
        $digest = Hash::ripemd160($privateKey->getPublicKey()->getBuffer());

        return (new PayToPubKeyHashAddress($digest))->getAddress($network);
    }

    /**
     * Validate the given address.
     *
     * @param string                   $address
     * @param AbstractNetwork|int|null $network
     *
     * @return bool
     */
    public static function validate(string $address, $network = null): bool
    {
        try {
            $addressCreator = new AddressCreator();
            $addressCreator->fromString($address, $network);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
