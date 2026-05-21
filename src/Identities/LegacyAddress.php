<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Identities;

use ArkEcosystem\Crypto\Binary\UnsignedInteger\Writer;
use BitWasp\Bitcoin\Base58;
use BitWasp\Bitcoin\Crypto\Hash;
use BitWasp\Buffertools\Buffer;

class LegacyAddress
{
    public static function fromPassphrase(string $passphrase, int $pubKeyHash): string
    {
        return static::fromPrivateKey(PrivateKey::fromPassphrase($passphrase), $pubKeyHash);
    }

    public static function fromPublicKey(string $publicKey, int $pubKeyHash): string
    {
        $ripemd160 = Hash::ripemd160(new Buffer(hex2bin($publicKey)));
        $seed = Writer::bit8($pubKeyHash) . $ripemd160->getBinary();

        return Base58::encodeCheck(new Buffer($seed));
    }

    public static function fromPrivateKey(PrivateKey $privateKey, int $pubKeyHash): string
    {
        return static::fromPublicKey($privateKey->publicKey, $pubKeyHash);
    }

    public static function validate(string $address, int $pubKeyHash): bool
    {
        try {
            $decoded = Base58::decodeCheck($address);

            if ($decoded->getSize() !== 21) {
                return false;
            }

            return ord($decoded->getBinary()[0]) === $pubKeyHash;
        } catch (\Exception) {
            return false;
        }
    }
}
