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
use ArkEcosystem\Crypto\Configuration\Network as NetworkConfiguration;
use ArkEcosystem\Crypto\Networks\AbstractNetwork;
use ArkEcosystem\Crypto\Utils\Base58;
use ArkEcosystem\Crypto\Utils\Hash;
use ArkEcosystem\Crypto\Utils\Secp256k1;
use BN\BN;
use Elliptic\EC\KeyPair;
use InvalidArgumentException;

/**
 * This is the private key class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class PrivateKey
{
    /**
     * Marks a WIF as belonging to a compressed public key.
     */
    private const COMPRESSED_SUFFIX = "\x01";

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
     * Derive the private key for the given passphrase.
     *
     * @param string $passphrase
     *
     * @return self
     */
    public static function fromPassphrase(string $passphrase): self
    {
        return static::fromHex(Hash::sha256($passphrase));
    }

    /**
     * Create a private key instance from a hex string.
     *
     * @param Bytes|string $privateKey
     *
     * @return self
     */
    public static function fromHex($privateKey): self
    {
        $privateKey = $privateKey instanceof Bytes ? $privateKey->getHex() : $privateKey;

        if (! ctype_xdigit($privateKey) || strlen($privateKey) !== 64) {
            throw new InvalidArgumentException('The given value is not a valid private key.');
        }

        $scalar = new BN($privateKey, 16);

        if ($scalar->isZero() || $scalar->cmp(Secp256k1::curve()->n) >= 0) {
            throw new InvalidArgumentException('The given value is not a valid private key.');
        }

        return new self(Secp256k1::curve()->keyFromPrivate($privateKey, 'hex'));
    }

    /**
     * Derive the private key for the given WIF.
     *
     * @param string               $wif
     * @param AbstractNetwork|null $network
     *
     * @return self
     */
    public static function fromWif(string $wif, ?AbstractNetwork $network = null): self
    {
        $network = $network ?? NetworkConfiguration::get();
        $payload = Base58::decodeCheck($wif);

        if (! in_array(strlen($payload), [33, 34], true)) {
            throw new InvalidArgumentException('The given value is not a valid WIF.');
        }

        if (bin2hex($payload[0]) !== strtolower($network->getPrivByte())) {
            throw new InvalidArgumentException('The given WIF does not belong to the network.');
        }

        if (strlen($payload) === 34 && $payload[33] !== self::COMPRESSED_SUFFIX) {
            throw new InvalidArgumentException('The given value is not a valid WIF.');
        }

        return static::fromHex(bin2hex(substr($payload, 1, 32)));
    }

    /**
     * Get the private key as hex.
     *
     * @return string
     */
    public function getHex(): string
    {
        return str_pad($this->keyPair->getPrivate('hex'), 64, '0', STR_PAD_LEFT);
    }

    /**
     * Get the private key as binary.
     *
     * @return string
     */
    public function getBinary(): string
    {
        return (string) hex2bin($this->getHex());
    }

    /**
     * Get the private key bytes.
     *
     * @return Bytes
     */
    public function getBuffer(): Bytes
    {
        return new Bytes($this->getBinary());
    }

    /**
     * Get the public key that belongs to this private key.
     *
     * @return PublicKey
     */
    public function getPublicKey(): PublicKey
    {
        return PublicKey::fromKeyPair($this->keyPair);
    }

    /**
     * Sign the given 32-byte hash with deterministic (RFC 6979), low-S ECDSA.
     *
     * @param Bytes|string $hash
     *
     * @return Bytes the DER encoded signature
     */
    public function sign($hash): Bytes
    {
        $hash = $hash instanceof Bytes ? $hash->getHex() : bin2hex($hash);

        $signature = $this->keyPair->sign($hash, ['canonical' => true]);

        return Bytes::fromHex($signature->toDER('hex'));
    }

    /**
     * Encode the private key as a compressed WIF.
     *
     * @param AbstractNetwork|null $network
     *
     * @return string
     */
    public function toWif(?AbstractNetwork $network = null): string
    {
        $network = $network ?? NetworkConfiguration::get();

        return Base58::encodeCheck(hex2bin($network->getPrivByte()).$this->getBinary().self::COMPRESSED_SUFFIX);
    }
}
