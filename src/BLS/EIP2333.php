<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\BLS;

use ArkEcosystem\Crypto\BLS\Fields\Fp;

/**
 * EIP-2333 BLS12-381 key derivation.
 * Mnemonic → PBKDF2 (BIP-39) seed → deriveMaster → deriveChild(0) → BLS private key.
 */
final class EIP2333
{
    /**
     * BLS-SIG-KEYGEN-SALT- initial salt constant (not the sha256 of it yet).
     */
    private const SALT_INIT = 'BLS-SIG-KEYGEN-SALT-';

    /**
     * Derives a BLS12-381 private key from a BIP-39 mnemonic.
     * Returns 32 bytes (raw binary).
     */
    public static function deriveBlsPrivateKey(string $mnemonic): string
    {
        $seed   = self::mnemonicToSeed($mnemonic);  // 64-byte BIP-39 seed
        $master = self::deriveMaster($seed);         // 32-byte master key
        return self::deriveChild($master, 0);        // 32-byte child key at index 0
    }

    /**
     * BIP-39: mnemonic → 64-byte seed via PBKDF2-HMAC-SHA512 with salt "mnemonic".
     */
    public static function mnemonicToSeed(string $mnemonic): string
    {
        return hash_pbkdf2('sha512', $mnemonic, 'mnemonic', 2048, 64, true);
    }

    /**
     * EIP-2333 deriveMaster: hkdfModR(seed).
     * Returns 32 bytes.
     */
    public static function deriveMaster(string $seed): string
    {
        return self::hkdfModR($seed);
    }

    /**
     * EIP-2333 deriveChild: hkdfModR(parentSKToLamportPK(parentSK, index)).
     * Returns 32 bytes.
     */
    public static function deriveChild(string $parentKey, int $index): string
    {
        return self::hkdfModR(self::parentSKToLamportPK($parentKey, $index));
    }

    /**
     * EIP-2333 hkdfModR:
     * salt = sha256("BLS-SIG-KEYGEN-SALT-")
     * Repeat: salt = sha256(salt), okm = HKDF-SHA256(IKM=ikm||0x00, salt, info="\x00\x30", 48)
     * Until SK = os2ip(okm) mod r != 0
     */
    public static function hkdfModR(string $ikm, string $keyInfo = ''): string
    {
        $r     = Fp::order();
        $input = $ikm . "\x00";           // IKM padded per EIP-2333
        $info  = $keyInfo . "\x00\x30";  // key_info || I2OSP(48, 2)
        $salt  = self::SALT_INIT;        // will be sha256'd before first use

        while (true) {
            $salt = hash('sha256', $salt, true);
            $okm  = hash_hkdf('sha256', $input, 48, $info, $salt);
            $sk   = gmp_mod(gmp_init(bin2hex($okm), 16), $r);
            if (gmp_sign($sk) !== 0) {
                return hex2bin(str_pad(gmp_strval($sk, 16), 64, '0', STR_PAD_LEFT));
            }
        }
    }

    /**
     * EIP-2333 ikmToLamportSK: HKDF-SHA256(IKM=ikm, salt=salt, info='', 255*32).
     * Returns array of 255 × 32-byte strings.
     *
     * @return string[]
     */
    private static function ikmToLamportSK(string $ikm, string $salt): array
    {
        $okm    = hash_hkdf('sha256', $ikm, 255 * 32, '', $salt);
        $chunks = [];
        for ($i = 0; $i < 255; $i++) {
            $chunks[] = substr($okm, $i * 32, 32);
        }
        return $chunks;
    }

    /**
     * EIP-2333 parentSKToLamportPK.
     * Returns 32 bytes (sha256 of concatenated hashed lamport keys).
     */
    private static function parentSKToLamportPK(string $parentSK, int $index): string
    {
        $salt   = pack('N', $index);         // I2OSP(index, 4)
        $notIkm = self::bitwiseNot($parentSK);

        $lamport0 = self::ikmToLamportSK($parentSK, $salt);
        $lamport1 = self::ikmToLamportSK($notIkm, $salt);

        $hashed = '';
        foreach (array_merge($lamport0, $lamport1) as $part) {
            $hashed .= hash('sha256', $part, true);
        }

        return hash('sha256', $hashed, true);
    }

    private static function bitwiseNot(string $bytes): string
    {
        $result = '';
        $len    = strlen($bytes);
        for ($i = 0; $i < $len; $i++) {
            $result .= chr(~ord($bytes[$i]) & 0xff);
        }
        return $result;
    }
}
