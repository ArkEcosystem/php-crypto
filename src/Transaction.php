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
use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PrivateKey;
use BitWasp\Bitcoin\Crypto\Hash;
use BitWasp\Bitcoin\Key\PublicKeyFactory;
use BitWasp\Bitcoin\Signature\SignatureFactory;
use BitWasp\Buffertools\Buffer;
use stdClass;

/**
 * This is the transaction class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class Transaction
{
    /**
     * Perform AIP11 compliant serialisation.
     *
     * @return \BitWasp\Buffertools\Buffer
     */
    public function serialise(): Buffer
    {
        return Serialiser::new($serialised)->deserialise();
    }

    /**
     * Perform AIP11 compliant deserialisation.
     *
     * @return stdClass
     */
    public function deserialise(string $serialised): stdClass
    {
        return Deserialiser::new($serialised)->deserialise();
    }

    /**
     * Convert the byte representation to a unique identifier.
     *
     * @return string
     */
    public function getId(): string
    {
        return Hash::sha256($this->toBytes(false, false))->getHex();
    }

    /**
     * Sign the transaction using the given secret.
     *
     * @param \BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PrivateKey $keys
     *
     * @return \ArkEcosystem\Crypto\Transaction
     */
    public function sign(PrivateKey $keys): self
    {
        $transaction     = Hash::sha256($this->toBytes());
        $this->signature = $keys->sign($transaction)->getBuffer()->getHex();

        return $this;
    }

    /**
     * Sign the transaction using the given second secret.
     *
     * @param \BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PrivateKey $keys
     *
     * @return \ArkEcosystem\Crypto\Transaction
     */
    public function secondSign(PrivateKey $keys): self
    {
        $transaction         = Hash::sha256($this->toBytes(false));
        $this->signSignature = $keys->sign($transaction)->getBuffer()->getHex();

        return $this;
    }

    /**
     * Verify the transaction validity.
     *
     * @return bool
     */
    public function verify(): bool
    {
        $publicKey = PublicKeyFactory::fromHex($this->senderPublicKey);

        return $publicKey->verify(
            Hash::sha256($this->toBytes()),
            SignatureFactory::fromHex($this->signature)
        );
    }

    /**
     * Verify the transaction validity with a second signature.
     *
     * @param string $secondPublicKeyHex
     *
     * @return bool
     */
    public function secondVerify(string $secondPublicKey): bool
    {
        $secondPublicKey = PublicKeyFactory::fromHex($secondPublicKey);

        return $secondPublicKey->verify(
            Hash::sha256($this->toBytes(false)),
            SignatureFactory::fromHex($this->signSignature)
        );
    }

    /**
     * Convert the transaction to its byte representation.
     *
     * @param bool $skipSignature
     * @param bool $skipSecondSignature
     *
     * @return string
     */
    public function toBytes(bool $skipSignature = true, bool $skipSecondSignature = true): Buffer
    {
        // TODO: replace pack calls with binary writer - see serialiser
        $bytes = '';
        $bytes .= pack('h', $this->type);
        $bytes .= pack('V', $this->timestamp);
        $bytes .= pack('H'.strlen($this->senderPublicKey), $this->senderPublicKey);

        if (isset($this->recipientId)) {
            $bytes .= \BitWasp\Bitcoin\Base58::decodeCheck($this->recipientId)->getBinary();
        } else {
            $bytes .= pack('x21');
        }

        if (isset($this->vendorField) && strlen($this->vendorField) < 64) {
            $bytes .= $this->vendorField;
            $vendorFieldLength = strlen($this->vendorField);

            if ($vendorFieldLength < 64) {
                $bytes .= pack('x'.(64 - $vendorFieldLength));
            }
        } else {
            $bytes .= pack('x64');
        }

        $bytes .= pack('P', $this->amount);
        $bytes .= pack('P', $this->fee);

        if (Types::SECOND_SIGNATURE_REGISTRATION === $this->type) {
            $assetSigPubKey = $this->asset->signature->publicKey;

            $bytes .= pack('H'.strlen($assetSigPubKey), $assetSigPubKey);
        }

        if (Types::DELEGATE_REGISTRATION === $this->type) {
            $bytes .= $this->asset->delegate->username;
        }

        if (Types::VOTE === $this->type) {
            $bytes .= implode('', $this->asset->votes);
        }

        if (Types::MULTI_SIGNATURE_REGISTRATION === $this->type) {
            $bytes .= pack('C', $this->asset->multisignature->min);
            $bytes .= pack('C', $this->asset->multisignature->lifetime);
            $bytes .= implode('', $this->asset->multisignature->keysgroup);
        }

        if (!$skipSignature && $this->signature) {
            $bytes .= pack('H'.strlen($this->signature), $this->signature);
        }

        if (!$skipSecondSignature && isset($this->signSignature)) {
            $bytes .= pack('H'.strlen($this->signSignature), $this->signSignature);
        }

        return new Buffer($bytes);
    }

    /**
     * Parse the signature, second signature and multi signatures.
     *
     * @param string $serialized
     * @param int    $startOffset
     *
     * @return \ArkEcosystem\Crypto\Transaction
     */
    public function parseSignatures(string $serialized, int $startOffset): self
    {
        $this->signature = substr($serialized, $startOffset);

        $multiSignatureOffset = 0;

        if (0 === strlen($this->signature)) {
            unset($this->signature);
        } else {
            $signatureLength        = intval(substr($this->signature, 2, 2), 16) + 2;
            $this->signature        = substr($serialized, $startOffset, $signatureLength * 2);
            $multiSignatureOffset += $signatureLength * 2;
            $this->secondSignature = substr($serialized, $startOffset + $signatureLength * 2);

            if (0 === strlen($this->secondSignature)) {
                unset($this->secondSignature);
            } else {
                if ('ff' === substr($this->secondSignature, 0, 2)) {
                    unset($this->secondSignature);
                } else {
                    $secondSignatureLength        = intval(substr($this->secondSignature, 2, 2), 16) + 2;
                    $this->secondSignature        = substr($this->secondSignature, 0, $secondSignatureLength * 2);
                    $multiSignatureOffset += $secondSignatureLength * 2;
                }
            }

            $signatures = substr($serialized, $startOffset + $multiSignatureOffset);

            if (0 === strlen($signatures)) {
                return $this;
            }

            if ('ff' !== substr($signatures, 0, 2)) {
                return $this;
            }

            $signatures              = substr($signatures, 2);
            $this->signatures        = [];

            $moreSignatures = true;
            while ($moreSignatures) {
                $mLength = intval(substr($signatures, 2, 2), 16);

                if ($mLength > 0) {
                    $this->signatures[] = substr($signatures, 0, ($mLength + 2) * 2);
                } else {
                    $moreSignatures = false;
                }

                $signatures = substr($signatures, ($mLength + 2) * 2);
            }
        }

        return $this;
    }
}
