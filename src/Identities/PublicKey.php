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

use ArkEcosystem\Crypto\Binary\Bytes;
use ArkEcosystem\Crypto\Utils\Secp256k1;
use Elliptic\EC\KeyPair;
use InvalidArgumentException;
use Throwable;

/**
 * This is the public key class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class PublicKey
{
    /**
     * @var KeyPair
     */
    private $keyPair;

    /**
     * @param KeyPair $keyPair
     */
    private function __construct(KeyPair $keyPair)
    {
        $this->keyPair = $keyPair;
    }

    /**
     * Derive the public from the given passphrase.
     *
     * @param string $passphrase
     *
     * @return self
     */
    public static function fromPassphrase(string $passphrase): self
    {
        return PrivateKey::fromPassphrase($passphrase)->getPublicKey();
    }

    /**
     * Create a public key instance from a multi-signature asset.
     *
     * @param int   $min
     * @param array $publicKeys
     *
     * @return self
     */
    public static function fromMultiSignatureAsset(int $min, array $publicKeys): self
    {
        $minKey = static::fromPassphrase('0'.dechex($min));
        $keys   = [$minKey->getHex(), ...$publicKeys];

        $curve = Secp256k1::curve()->curve;
        $P     = $curve->jpoint(null, null, null);

        foreach ($keys as $publicKey) {
            $P = $P->add($curve->decodePoint($publicKey, 'hex'));
        }

        return static::fromHex(bin2hex(implode(array_map('chr', $P->encodeCompressed(true)))));
    }

    /**
     * Create a public key instance from a compressed or uncompressed hex string.
     *
     * @param Bytes|string $publicKey
     *
     * @return self
     */
    public static function fromHex($publicKey): self
    {
        $publicKey = $publicKey instanceof Bytes ? $publicKey->getHex() : $publicKey;

        if (! ctype_xdigit($publicKey) || ! in_array(strlen($publicKey), [66, 130], true)) {
            throw new InvalidArgumentException('The given value is not a valid public key.');
        }

        try {
            $keyPair = Secp256k1::curve()->keyFromPublic($publicKey, 'hex');
        } catch (Throwable $e) {
            throw new InvalidArgumentException('The given value is not a valid public key.', 0, $e);
        }

        // secp256k1 has a cofactor of 1, so any point on the curve is in the prime-order group.
        if (! $keyPair->getPublic()->validate()) {
            throw new InvalidArgumentException('The given value is not a valid public key.');
        }

        return new self($keyPair);
    }

    /**
     * Create a public key instance from the public half of the given key pair.
     *
     * @param KeyPair $keyPair
     *
     * @return self
     */
    public static function fromKeyPair(KeyPair $keyPair): self
    {
        return static::fromHex($keyPair->getPublic(true, 'hex'));
    }

    /**
     * Get the compressed public key as hex.
     *
     * @return string
     */
    public function getHex(): string
    {
        return $this->keyPair->getPublic(true, 'hex');
    }

    /**
     * Get the compressed public key as binary.
     *
     * @return string
     */
    public function getBinary(): string
    {
        return (string) hex2bin($this->getHex());
    }

    /**
     * Get the compressed public key bytes.
     *
     * @return Bytes
     */
    public function getBuffer(): Bytes
    {
        return new Bytes($this->getBinary());
    }

    /**
     * Verify a DER encoded ECDSA signature over the given 32-byte hash.
     *
     * @param Bytes|string $hash
     * @param Bytes|string $signature
     *
     * @return bool
     */
    public function verify($hash, $signature): bool
    {
        $hash      = $hash instanceof Bytes ? $hash->getHex() : bin2hex($hash);
        $signature = $signature instanceof Bytes ? $signature->getHex() : $signature;

        if (! ctype_xdigit($signature)) {
            return false;
        }

        try {
            return $this->keyPair->verify($hash, $signature);
        } catch (Throwable $e) {
            return false;
        }
    }
}
