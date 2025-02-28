<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Identities;

use ArkEcosystem\Crypto\Networks\AbstractNetwork;
use ArkEcosystem\Crypto\Utils\Hash;
use Elliptic\EC;
use Elliptic\EC\KeyPair;
use Elliptic\EC\Signature;

class PrivateKey
{
    private KeyPair $keyPair;

    public function __construct(KeyPair $keyPair)
    {
        $this->keyPair = $keyPair;
    }

    public function getHex(): string
    {
        return $this->keyPair->getPrivate('hex');
    }

    public function toWif(AbstractNetwork $network, bool $compressed = true): string
    {
        $prefix = $network->getPrivByte();
        $suffix = ''; // Suffix for compressed keys
        if ($compressed) {
            $suffix = '01';
        }

        $privateKey  = $this->keyPair->getPrivate('hex');
        $extendedKey = $prefix.$privateKey.$suffix;

        // Double SHA256 hashing
        $hash1 = Hash::sha256(hex2bin($extendedKey));
        $hash2 = Hash::sha256(hex2bin($hash1));

        // Append first 4 bytes of second hash as checksum
        $checksum = substr($hash2, 0, 8);

        // Final WIF format key
        $finalKey = $extendedKey.$checksum;

        // Encode to Base58
        return self::base58Encode(hex2bin($finalKey));
    }

    public function sign($message): string
    {
        $signature = $this->keyPair->sign($message);

        return $signature->toDER('hex');
    }

    public function signCompact($message): Signature
    {
        $signature = $this->keyPair->sign($message);

        return $signature;
    }

    /**
     * Derive the private key for the given passphrase.
     *
     * @param string $passphrase
     *
     * @return PrivateKey
     */
    public static function fromPassphrase(string $passphrase): self
    {
        $passphrase = Hash::sha256($passphrase);

        return new self(self::ec()->keyFromPrivate($passphrase));
    }

    /**
     * Create a private key instance from a hex string.
     *
     * @param string $privateKey
     *
     * @return PrivateKey
     */
    public static function fromHex($privateKey): self
    {
        return new self(self::ec()->keyFromPrivate($privateKey));
    }

    /**
     * Derive the private key for the given WIF.
     *
     * @param string $wif
     *
     * @return PrivateKey
     */
    public static function fromWif(string $wif): self
    {
        $wif = self::base58Decode($wif);
        $wif = bin2hex($wif);
        $wif = substr($wif, 0, -8);
        $wif = substr($wif, 2, 64);

        if (strlen($wif) === 66) {
            fwrite(STDERR, $wif."\n");
            $wif = substr($wif, 0, -2);
        }

        return self::fromHex($wif);
    }

    public function getPublicKey(): PublicKey
    {
        return PublicKey::fromKeyPair($this->keyPair);
    }

    private static function base58Decode($input)
    {
        $alphabet = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';
        $base     = (string) strlen($alphabet);
        $length   = strlen($input);
        $num      = '0';

        for ($i = 0; $i < $length; $i++) {
            $pos = strpos($alphabet, $input[$i]);
            if ($pos === false) {
                throw new \Exception('Invalid character in Base58 string.');
            }
            $num = bcmul($num, $base, 0);
            $num = bcadd($num, (string) $pos, 0);
        }

        $decoded = '';
        while (bccomp($num, '0') > 0) {
            $rem     = bcmod($num, '256');
            $decoded = chr((int) $rem).$decoded;
            $num     = bcdiv($num, '256', 0);
        }

        // Handle leading zeros
        $leadingZeros = 0;
        for ($i = 0; $i < $length && $input[$i] === '1'; $i++) {
            $leadingZeros++;
        }

        return str_repeat("\0", $leadingZeros).$decoded;
    }

    private static function base58Encode($input)
    {
        $alphabet = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';
        $num      = gmp_init(bin2hex($input), 16);
        $base     = gmp_init(strlen($alphabet));
        $encoded  = '';

        while (gmp_cmp($num, 0) > 0) {
            list($num, $rem) = gmp_div_qr($num, $base);
            $encoded         = $alphabet[gmp_intval($rem)].$encoded;
        }

        // Handle leading zeros
        foreach (str_split($input) as $char) {
            if ($char === "\0") {
                $encoded = '1'.$encoded;
            } else {
                break;
            }
        }

        return $encoded;
    }

    private static function ec(): EC
    {
        return new EC('secp256k1');
    }
}
