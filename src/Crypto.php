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

namespace ArkEcosystem\Crypto;

use ArkEcosystem\Crypto\Enums\TransactionTypes;
use ArkEcosystem\Crypto\Utils\Base58;
use BitWasp\Bitcoin\Address\PayToPubKeyHashAddress;
use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PrivateKey;
use BitWasp\Bitcoin\Crypto\Hash;
use BitWasp\Bitcoin\Key\PrivateKeyFactory;
use BitWasp\Bitcoin\Key\PublicKeyFactory;
use BitWasp\Bitcoin\Network\NetworkFactory;
use BitWasp\Bitcoin\Network\NetworkInterface;
use BitWasp\Bitcoin\Signature\SignatureFactory;
use BitWasp\Buffertools\Buffer;
use stdClass;

/**
 * This is the crypto class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class Crypto
{
    /**
     * Compute an ARK Address from the given public key.
     *
     * @param string $secret
     * @param int    $version
     *
     * @return string
     */
    public static function addressFromPublicKey(string $publicKey, int $version = 0x17): string
    {
        $ripemd160 = hash('ripemd160', hex2bin($publicKey), true);
        $seed      = pack('C*', $version).$ripemd160;

        return Base58::encodeCheck($seed);
    }

    /**
     * Validate an ARK Address.
     *
     * @param string $address
     * @param string $networkVersion
     *
     * @return bool
     */
    public static function validateAddress(string $address, string $networkVersion = '17')
    {
        $address    = new Buffer(Base58::decode($address));
        $prefixByte = $address->slice(0, 1)->getHex();

        return $prefixByte === $networkVersion;
    }

    /**
     * Compute an WIF address from the given secret.
     *
     * @param string $secret
     * @param int    $wif
     *
     * @return string
     */
    public static function wif(string $secret, int $wif = 0xaa): string
    {
        $secret = hash('sha256', $secret, true);
        $seed   = pack('C*', $wif).$secret.pack('C*', 0x01);

        return Base58::encodeCheck($seed);
    }

    /**
     * [getKeys description].
     *
     * @param string $secret
     *
     * @return \BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PrivateKey
     */
    public static function getKeys(string $secret): PrivateKey
    {
        $seed = static::bchexdec(hash('sha256', $secret));

        return PrivateKeyFactory::fromInt($seed, true);
    }

    /**
     * [getAddress description].
     *
     * @param \BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PrivateKey $privateKey
     * @param NetworkInterface|null                                        $network
     *
     * @return [type]
     */
    public static function getAddress(PrivateKey $privateKey, NetworkInterface $network = null)
    {
        $publicKey = $privateKey->getPublicKey();
        $digest    = Hash::ripemd160(new Buffer($publicKey->getBinary()));

        if (!$network) {
            $network = NetworkFactory::create('17', '00', '00');
        }

        return (new PayToPubKeyHashAddress($digest))->getAddress($network);
    }

    /**
     * [getBytes description].
     *
     * @param [type] $transaction
     * @param bool   $skipSignature
     * @param bool   $skipSecondSignature
     *
     * @return [type]
     */
    public static function getBytes(object $transaction, $skipSignature = true, $skipSecondSignature = true)
    {
        $out = '';
        $out .= pack('h', $transaction->type);
        $out .= pack('V', $transaction->timestamp);
        $out .= pack('H'.strlen($transaction->senderPublicKey), $transaction->senderPublicKey);

        // TODO: requester public key

        if ($transaction->recipientId) {
            $out .= \BitWasp\Bitcoin\Base58::decodeCheck($transaction->recipientId)->getBinary();
        } else {
            $out .= pack('x21');
        }

        if (isset($transaction->vendorField) && strlen($transaction->vendorField) < 64) {
            $out .= $transaction->vendorField;
            $vendorFieldLength = strlen($transaction->vendorField);
            if ($vendorFieldLength < 64) {
                $out .= pack('x'.(64 - $vendorFieldLength));
            }
        } else {
            $out .= pack('x64');
        }

        $out .= pack('P', $transaction->amount);
        $out .= pack('P', $transaction->fee);

        if (TransactionTypes::SECOND_SIGNATURE_REGISTRATION === $transaction->type) { // second signature
            $assetSigPubKey = $transaction->asset['signature']['publicKey'];

            $out .= pack('H'.strlen($assetSigPubKey), $assetSigPubKey);
        }

        if (TransactionTypes::DELEGATE_REGISTRATION === $transaction->type) {
            $out .= $transaction->asset['delegate']['username'];
        }

        if (TransactionTypes::VOTE === $transaction->type) {
            $out .= implode('', $transaction->asset['votes']);
        }

        if (TransactionTypes::MULTI_SIGNATURE === $transaction->type) {
            $out .= pack('C', $transaction->asset['multisignature']['min']);
            $out .= pack('C', $transaction->asset['multisignature']['lifetime']);
            $out .= implode('', $transaction->asset['multisignature']['keysgroup']);
        }

        if (!$skipSignature && $transaction->signature) {
            $out .= pack('H'.strlen($transaction->signature), $transaction->signature);
        }

        if (!$skipSecondSignature && isset($transaction->signSignature)) {
            $out .= pack('H'.strlen($transaction->signSignature), $transaction->signSignature);
        }

        return $out;
    }

    /**
     * [sign description].
     *
     * @param [type] $transaction
     * @param [type] $keys
     *
     * @return [type]
     */
    public static function sign($transaction, $keys): stdClass
    {
        $txBytes                = static::getBytes($transaction);
        $transaction->signature = $keys->sign(Hash::sha256(new Buffer($txBytes)))->getBuffer()->getHex();

        return $transaction;
    }

    /**
     * [secondSign description].
     *
     * @param [type] $transaction
     * @param [type] $keys
     *
     * @return [type]
     */
    public static function secondSign($transaction, $keys): stdClass
    {
        $txBytes                    = static::getBytes($transaction, false);
        $transaction->signSignature = $keys->sign(Hash::sha256(new Buffer($txBytes)))->getBuffer()->getHex();

        return $transaction;
    }

    /**
     * [verify description].
     *
     * @param object $transaction
     *
     * @return [type]
     */
    public static function verify(object $transaction): bool
    {
        $publicKey = PublicKeyFactory::fromHex($transaction->senderPublicKey);
        $bytes     = static::getBytes($transaction);

        return $publicKey->verify(
            new Buffer(hash('sha256', $bytes, true)),
            SignatureFactory::fromHex($transaction->signature)
        );
    }

    /**
     * [secondVerify description].
     *
     * @param object $transaction
     * @param string $secondPublicKeyHex
     *
     * @return bool
     */
    public static function secondVerify(object $transaction, string $secondPublicKeyHex): bool
    {
        $secondPublicKeys = PublicKeyFactory::fromHex($secondPublicKeyHex);
        $bytes            = static::getBytes($transaction, false);

        return $secondPublicKeys->verify(
            new Buffer(hash('sha256', $bytes, true)),
            SignatureFactory::fromHex($transaction->signSignature)
        );
    }

    /**
     * hexdec but for integers that are bigger than the largest PHP integer
     * https://stackoverflow.com/questions/1273484/large-hex-values-with-php-hexdec.
     *
     * @param $hex
     *
     * @return int|string
     */
    private static function bchexdec(string $hex): string
    {
        $dec = '0';
        $len = strlen($hex);
        for ($i = 1; $i <= $len; ++$i) {
            $dec = bcadd($dec, bcmul((string) (hexdec($hex[$i - 1])), bcpow('16', (string) ($len - $i))));
        }

        return $dec;
    }
}
