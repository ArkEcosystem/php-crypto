<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Identities;

use Elliptic\EC;
use Elliptic\EC\KeyPair;

class PublicKey
{
    private KeyPair $keyPair;

    public function __construct(KeyPair $keyPair)
    {
        $this->keyPair = $keyPair;
    }

    public function getHex(bool $compact = true): string
    {
        return $this->keyPair->getPublic($compact, 'hex');
    }

    public static function fromKeyPair(KeyPair $keyPair): self
    {
        return new self($keyPair);
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

        $keys = [$minKey->getHex()];
        foreach ($publicKeys as $publicKey) {
            $keys[] = static::fromHex($publicKey)->getHex();
        }

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
     * @param string $publicKey
     *
     * @return self
     */
    public static function fromHex($publicKey): self
    {
        $keyPair = new KeyPair(self::ec(), ['pub' => $publicKey, 'pubEnc' => 'hex']);

        return new self($keyPair);
        // return new self(self::ec()->keyFromPublic($publicKey));
    }

    private static function ec(): EC
    {
        return new EC('secp256k1');
    }
}
