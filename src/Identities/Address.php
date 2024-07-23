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

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;
use ArkEcosystem\Crypto\Networks\AbstractNetwork;
use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PrivateKey as EccPrivateKey;
use BitWasp\Bitcoin\Crypto\Hash;
use BitWasp\Buffertools\Buffer;
use Elliptic\EC;
use kornrunner\Keccak;

class Address
{
    /**
     * Derive the address from the given passphrase.
     *
     * @param string               $passphrase
     *
     * @return string
     */
    public static function fromPassphrase(string $passphrase): string
    {
        return static::fromPrivateKey(PrivateKey::fromPassphrase($passphrase));
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
     *
     * @return string
     */
    public static function fromPublicKey(string $publicKey): string
    {
        // Convert the public key to a byte array
        $publicKeyBytes = hex2bin($publicKey);

        // Ensure the public key is uncompressed
        $ec                    = new EC('secp256k1');
        $key                   = $ec->keyFromPublic($publicKeyBytes);
        $uncompressedPublicKey = $key->getPublic(false, 'hex'); // Get uncompressed public key

        // Remove the prefix (0x04)
        $uncompressedPublicKey = substr($uncompressedPublicKey, 2);

        // Convert the public key to a byte array
        $uncompressedPublicKeyBytes = hex2bin($uncompressedPublicKey);

        // Hash the public key using Keccak-256
        $keccakHash = Keccak::hash($uncompressedPublicKeyBytes, 256);

        // Take the last 40 characters of the hash (20 bytes)
        $address = substr($keccakHash, -40);

        // Prefix with 0x
        $address = '0x'.$address;

        // Convert to checksum address
        return self::toChecksumAddress($address);
    }

    /**
     * Derive the address from the given private key.
     *
     * @param EccPrivateKey $privateKey
     *
     * @return string
     */
    public static function fromPrivateKey(EccPrivateKey $privateKey): string
    {
        $publicKey = $privateKey->getPublicKey()->getHex();

        return static::fromPublicKey($publicKey);
    }

    /**
     * Validate the given address.
     *
     * @param string $address
     * @param AbstractNetwork|int|null $network
     *
     * @return bool
     */
    public static function validate(string $address): bool
    {
        // Simple validation to check if the address starts with 0x and is 42 characters long
        return preg_match('/^0x[a-fA-F0-9]{40}$/', $address) === 1;
    }

    /**
     * Convert to checksum address.
     *
     * @param string $address
     *
     * @return string
     */
    public static function toChecksumAddress(string $address): string
    {
        $address         = strtolower(substr($address, 2));
        $hash            = Keccak::hash($address, 256);
        $checksumAddress = '0x';

        for ($i = 0; $i < 40; $i++) {
            if (intval($hash[$i], 16) >= 8) {
                $checksumAddress .= strtoupper($address[$i]);
            } else {
                $checksumAddress .= $address[$i];
            }
        }

        return $checksumAddress;
    }

    /**
     * Convert to hex string without 0x prefix.
     *
     * @param string $address
     *
     * @return string
     */
    public static function toBufferHexString(string $address): string
    {
        if (strpos($address, '0x') === 0) {
            $address = substr($address, 2);
        }

        return strtolower($address);
    }

    /**
     * Extract the address from a byte buffer.
     *
     * @param ByteBuffer $buffer
     *
     * @return string
     */
    public static function fromByteBuffer(ByteBuffer $buffer): string
    {
        $hexAddress = '0x'.(new Buffer(hex2bin($buffer->readHex(20 * 2))))->getHex();

        return self::toChecksumAddress($hexAddress);
    }
}
