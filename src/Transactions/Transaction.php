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

namespace ArkEcosystem\Crypto\Transactions;

use ArkEcosystem\Crypto\Enums\Types;
use BitWasp\Bitcoin\Base58;
use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PrivateKey;
use BitWasp\Bitcoin\Crypto\Hash;
use BitWasp\Bitcoin\Key\PublicKeyFactory;
use BitWasp\Bitcoin\Signature\SignatureFactory;
use BitWasp\Buffertools\Buffer;
use BrianFaust\Binary\Buffer\Writer\Buffer as Writer;

/**
 * This is the transaction class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class Transaction
{
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
     * Sign the transaction using the given passphrase.
     *
     * @param \BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PrivateKey $keys
     *
     * @return \ArkEcosystem\Crypto\Transactions\Transaction
     */
    public function sign(PrivateKey $keys): self
    {
        $transaction     = Hash::sha256($this->toBytes());
        $this->signature = $keys->sign($transaction)->getBuffer()->getHex();

        return $this;
    }

    /**
     * Sign the transaction using the given second passphrase.
     *
     * @param \BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PrivateKey $keys
     *
     * @return \ArkEcosystem\Crypto\Transactions\Transaction
     */
    public function secondSign(PrivateKey $keys): self
    {
        $transaction         = Hash::sha256($this->toBytes(false));
        $this->signSignature = $keys->sign($transaction)->getBuffer()->getHex();

        return $this;
    }

    /**
     * Verify the transaction.
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
     * Verify the transaction with a second public key.
     *
     * @param string $secondPublicKey
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
     * Parse the signature, second signature and multi signatures.
     *
     * @param string $serialized
     * @param int    $startOffset
     *
     * @return \ArkEcosystem\Crypto\Transactions\Transaction
     */
    public function parseSignatures(string $serialized, int $startOffset): self
    {
        $this->signature = substr($serialized, $startOffset);

        $multiSignatureOffset = 0;

        if (0 === strlen($this->signature)) {
            unset($this->signature);
        } else {
            $signatureLength       = intval(substr($this->signature, 2, 2), 16) + 2;
            $this->signature       = substr($serialized, $startOffset, $signatureLength * 2);
            $multiSignatureOffset += $signatureLength * 2;
            $this->secondSignature = substr($serialized, $startOffset + $signatureLength * 2);

            if (!$this->secondSignature || 0 === strlen($this->secondSignature)) {
                unset($this->secondSignature);
            } else {
                if ('ff' === substr($this->secondSignature, 0, 2)) {
                    unset($this->secondSignature);
                } else {
                    $secondSignatureLength = intval(substr($this->secondSignature, 2, 2), 16) + 2;
                    $this->secondSignature = substr($this->secondSignature, 0, $secondSignatureLength * 2);
                    $multiSignatureOffset += $secondSignatureLength * 2;
                }
            }

            $signatures = substr($serialized, $startOffset + $multiSignatureOffset);

            if (!$signatures || 0 === strlen($signatures)) {
                return $this;
            }

            if ('ff' !== substr($signatures, 0, 2)) {
                return $this;
            }

            $signatures       = substr($signatures, 2);
            $this->signatures = [];

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
        $buffer = new Writer();
        $buffer->writeUInt8($this->type);
        $buffer->writeUInt32($this->timestamp);
        $buffer->writeHex($this->senderPublicKey);

        if (isset($this->recipientId)) {
            $buffer->writeHex(Base58::decodeCheck($this->recipientId)->getHex());
        } else {
            $buffer->fill(21);
        }

        if (isset($this->vendorField) && strlen($this->vendorField) < 64) {
            $buffer->writeString($this->vendorField);
            $vendorFieldLength = strlen($this->vendorField);

            if ($vendorFieldLength < 64) {
                $buffer->fill(64 - $vendorFieldLength);
            }
        } else {
            $buffer->fill(64);
        }

        $buffer->writeUInt64($this->amount);
        $buffer->writeUInt64($this->fee);

        if (Types::SECOND_SIGNATURE_REGISTRATION === $this->type) {
            $buffer->writeHex($this->asset['signature']['publicKey']);
        }

        if (Types::DELEGATE_REGISTRATION === $this->type) {
            $buffer->writeString($this->asset['delegate']['username']);
        }

        if (Types::VOTE === $this->type) {
            $buffer->writeString(implode('', $this->asset['votes']));
        }

        if (Types::MULTI_SIGNATURE_REGISTRATION === $this->type) {
            $buffer->writeUInt8($this->asset['multisignature']['min']);
            $buffer->writeUInt8($this->asset['multisignature']['lifetime']);
            $buffer->writeString(implode('', $this->asset['multisignature']['keysgroup']));
        }

        if (!$skipSignature && $this->signature) {
            $buffer->writeHex($this->signature);
        }

        if (!$skipSecondSignature && isset($this->signSignature)) {
            $buffer->writeHex($this->signSignature);
        }

        return new Buffer($buffer->getBytes());
    }

    /**
     * Perform AIP11 compliant serialization.
     *
     * @return \BitWasp\Buffertools\Buffer
     */
    public function serialize(): Buffer
    {
        return Serializer::new($this->toArray())->serialize();
    }

    /**
     * Perform AIP11 compliant deserialization.
     *
     * @return \ArkEcosystem\Crypto\Transactions\Transaction
     */
    public static function deserialize(string $serialized): self
    {
        return Deserializer::new($serialized)->deserialize();
    }

    /**
     * Convert the transaction to its array representation.
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_filter([
            'amount'          => $this->amount,
            'asset'           => $this->asset ?? [],
            'fee'             => $this->fee,
            'id'              => $this->id,
            'network'         => $this->network,
            'recipientId'     => $this->recipientId ?? null,
            'secondSignature' => $this->secondSignature ?? null,
            'senderPublicKey' => $this->senderPublicKey,
            'signature'       => $this->signature,
            'signatures'      => $this->signatures ?? null,
            'signSignature'   => $this->signSignature ?? null,
            'timestamp'       => $this->timestamp,
            'type'            => $this->type,
            'vendorField'     => $this->vendorField ?? null,
            'version'         => $this->version,
        ], function ($element) {
            if (null !== $element) {
                return true;
            }

            return false;
        });
    }

    /**
     * Convert the transaction to its JSON representation.
     *
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }
}
