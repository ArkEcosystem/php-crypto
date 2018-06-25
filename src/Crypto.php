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

use ArkEcosystem\Crypto\Enums\Types;
use BitWasp\Bitcoin\Address\PayToPubKeyHashAddress;
use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PrivateKey;
use BitWasp\Bitcoin\Crypto\Hash;
use BitWasp\Bitcoin\Key\PublicKeyFactory;
use BitWasp\Bitcoin\Network\Network;
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
     * Derive an address from the given private key.
     *
     * @param \BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PrivateKey $privateKey
     * @param \BitWasp\Bitcoin\Network\NetworkInterface|null               $network
     *
     * @return string
     */
    public static function getAddress(PrivateKey $privateKey, NetworkInterface $network = null): string
    {
        $network   = $network ?? static::getDefaultNetwork();
        $publicKey = $privateKey->getPublicKey();
        $digest    = Hash::ripemd160(new Buffer($publicKey->getBinary()));

        return (new PayToPubKeyHashAddress($digest))->getAddress($network);
    }

    /**
     * Convert the transaction to its byte representation.
     *
     * @param object $transaction
     * @param bool   $skipSignature
     * @param bool   $skipSecondSignature
     *
     * @return string
     */
    public static function getBytes(object $transaction, bool $skipSignature = true, bool $skipSecondSignature = true): string
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

        if (Types::SECOND_SIGNATURE_REGISTRATION === $transaction->type) { // second signature
            $assetSigPubKey = $transaction->asset['signature']['publicKey'];

            $out .= pack('H'.strlen($assetSigPubKey), $assetSigPubKey);
        }

        if (Types::DELEGATE_REGISTRATION === $transaction->type) {
            $out .= $transaction->asset['delegate']['username'];
        }

        if (Types::VOTE === $transaction->type) {
            $out .= implode('', $transaction->asset['votes']);
        }

        if (Types::MULTI_SIGNATURE_REGISTRATION === $transaction->type) {
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
     * Sign the transaction using the given secret.
     *
     * @param object                                                       $transaction
     * @param \BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PrivateKey $keys
     *
     * @return \stdClass
     */
    public static function sign(object $transaction, PrivateKey $keys): stdClass
    {
        $txBytes                = static::getBytes($transaction);
        $transaction->signature = $keys->sign(Hash::sha256(new Buffer($txBytes)))->getBuffer()->getHex();

        return $transaction;
    }

    /**
     * Sign the transaction using the given second secret.
     *
     * @param object                                                       $transaction
     * @param \BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PrivateKey $keys
     *
     * @return \stdClass
     */
    public static function secondSign(object $transaction, PrivateKey $keys): stdClass
    {
        $txBytes                    = static::getBytes($transaction, false);
        $transaction->signSignature = $keys->sign(Hash::sha256(new Buffer($txBytes)))->getBuffer()->getHex();

        return $transaction;
    }

    /**
     * Verify the transaction validity.
     *
     * @param object $transaction
     *
     * @return bool
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
     * Verify the transaction validity with a second signature.
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
     * Get the default network used.
     *
     * @return \BitWasp\Bitcoin\Network\Network
     */
    public static function getDefaultNetwork(): Network
    {
        return NetworkFactory::create('17', '00', '00');
    }

    /**
     * [parseSignatures description].
     *
     * @param string $serialized
     * @param object $transaction
     * @param int    $startOffset
     *
     * @return object
     */
    public static function parseSignatures(string $serialized, object $transaction, int $startOffset): object
    {
        $transaction->signature = substr($serialized, $startOffset);

        $multiSignatureOffset = 0;

        if (0 === strlen($transaction->signature)) {
            unset($transaction->signature);
        } else {
            $length1                = intval(substr($transaction->signature, 2, 2), 16) + 2;
            $transaction->signature = substr($serialized, $startOffset, $length1 * 2);
            $multiSignatureOffset += $length1 * 2;
            $transaction->secondSignature = substr($serialized, $startOffset + $length1 * 2);

            if (0 === strlen($transaction->secondSignature)) {
                unset($transaction->secondSignature);
            } else {
                if ('ff' === substr($transaction->secondSignature, 0, 2)) { // start of multi-signature
                    unset($transaction->secondSignature);
                } else {
                    $length2                      = intval(substr($transaction->secondSignature, 2, 2), 16) + 2;
                    $transaction->secondSignature = substr($transaction->secondSignature, 0, $length2 * 2);
                    $multiSignatureOffset += $length2 * 2;
                }
            }

            $signatures = substr($serialized, $startOffset + $multiSignatureOffset);

            if (0 === strlen($signatures)) {
                return $transaction;
            }

            if ('ff' !== substr($signatures, 0, 2)) {
                return $transaction;
            }

            $signatures              = substr($signatures, 2);
            $transaction->signatures = [];

            $moreSignatures = true;
            while ($moreSignatures) {
                $mLength = intval(substr($signatures, 2, 2), 16);

                if ($mLength > 0) {
                    $transaction->signatures[] = substr($signatures, 0, ($mLength + 2) * 2);
                } else {
                    $moreSignatures = false;
                }

                $signatures = substr($signatures, ($mLength + 2) * 2);
            }
        }

        return $transaction;
    }
}
