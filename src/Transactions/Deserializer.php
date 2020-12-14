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

use ArkEcosystem\Crypto\Transactions\Types\Transaction;
use BitWasp\Bitcoin\Crypto\Hash;
use Konceiver\ByteBuffer\ByteBuffer;

/**
 * This is the deserializer class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class Deserializer
{
    /**
     * The transaction classes.
     *
     * @var array
     */
    private $transactionsClasses = [
        Types\Transfer::class,
        Types\SecondSignatureRegistration::class,
        Types\DelegateRegistration::class,
        Types\Vote::class,
        Types\MultiSignatureRegistration::class,
        Types\IPFS::class,
        Types\MultiPayment::class,
        Types\DelegateResignation::class,
        Types\HtlcLock::class,
        Types\HtlcClaim::class,
        Types\HtlcRefund::class,
    ];

    /**
     * Create a new deserializer instance.
     *
     * @param object $serialized
     */
    public function __construct(string $serialized)
    {
        $this->buffer = false === strpos($serialized, "\0")
            ? ByteBuffer::fromHex($serialized)
            : ByteBuffer::fromBinary($serialized);
    }

    /**
     * Create a new deserializer instance.
     *
     * @param string $serialized
     */
    public static function new(string $serialized)
    {
        return new static($serialized);
    }

    /**
     * Perform AIP11 compliant deserialization.
     *
     * @return Transaction
     */
    public function deserialize(): Transaction
    {
        $data = [];

        $this->deserializeCommon($data);

        $transactionClass  = $this->transactionsClasses[$data['type']];
        $transaction       = new $transactionClass();
        $transaction->data = $data;

        $this->deserializeVendorField($transaction);

        // Deserialize type specific parts
        $transaction->deserialize($this->buffer);

        $this->deserializeSignatures($transaction->data);

        if (! isset($transaction->data['amount'])) {
            $transaction->data['amount'] = '0';
        }

        $transaction = $this->handleVersionTwo($transaction);

        return $transaction;
    }

    private function deserializeCommon(array &$data): void
    {
        $this->buffer->skip(1);
        $data['version']         = $this->buffer->readUInt8();
        $data['network']         = $this->buffer->readUInt8();
        $data['typeGroup']       = $this->buffer->readUInt32();
        $data['type']            = $this->buffer->readUInt16();
        $data['nonce']           = strval($this->buffer->readUInt64());
        $data['senderPublicKey'] = $this->buffer->readHex(33 * 2);
        $data['fee']             = strval($this->buffer->readUInt64());
    }

    private function deserializeVendorField(Transaction $transaction): void
    {
        $vendorFieldLength = $this->buffer->readUInt8();
        if ($vendorFieldLength > 0) {
            if ($transaction->hasVendorField()) {
                $marker                              = $this->buffer->current();
                $transaction->data['vendorFieldHex'] = $this->buffer->readHex($vendorFieldLength * 2);
                $this->buffer->position($marker);
                $transaction->data['vendorField'] = $this->buffer->readHexString($vendorFieldLength * 2);
            } else {
                $this->buffer->skip($vendorFieldLength);
            }
        }
    }

    private function deserializeSignatures(array &$data): void
    {
        $this->deserializeSchnorrOrECDSA($data);
    }

    private function deserializeSchnorrOrECDSA(array &$data): void
    {
        if ($this->detectSchnorr()) {
            $this->deserializeSchnorr($data);
        } else {
            $this->deserializeECDSA($data);
        }
    }

    private function deserializeSchnorr(array &$data): void
    {
        if ($this->canReadNonMultiSignature($this->buffer)) {
            $data['signature'] = $this->buffer->readHex(64 * 2);
        }

        if ($this->canReadNonMultiSignature($this->buffer)) {
            $data['secondSignature'] = $this->buffer->readHex(64 * 2);
        }

        if ($this->buffer->remaining()) {
            if ($this->buffer->remaining() % 65 === 0) {
                $data['signatures'] = [];

                $count            = $this->buffer->remaining() / 65;
                $publicKeyIndexes = [];
                for ($i = 0; $i < $count; $i++) {
                    $multiSignaturePart = $this->buffer->readHex(65 * 2);
                    $publicKeyIndex     = intval(substr($multiSignaturePart, 0, 2), 16);

                    if (! isset($publicKeyIndexes[$publicKeyIndex])) {
                        $publicKeyIndexes[$publicKeyIndex] = true;
                    } else {
                        throw new \Exception('Duplicate participant in multisignature');
                    }

                    $data['signatures'][] = $multiSignaturePart;
                }
            } else {
                throw new \Exception('signature buffer not exhausted');
            }
        }
    }

    private function canReadNonMultiSignature(ByteBuffer $buffer)
    {
        return
            $buffer->remaining()
            && ($buffer->remaining() % 64 === 0 || $buffer->remaining() % 65 !== 0);
    }

    private function deserializeECDSA(array &$data): void
    {
        // Signature
        if ($this->buffer->remaining()) {
            $signatureLength   = $this->currentSignatureLength($this->buffer);
            $data['signature'] = $this->buffer->readHex($signatureLength * 2);
        }

        // Second Signature
        if ($this->buffer->remaining() && ! $this->beginningMultiSignature($this->buffer)) {
            $secondSignatureLength   = $this->currentSignatureLength($this->buffer);
            $data['secondSignature'] = $this->buffer->readHex($secondSignatureLength * 2);
        }

        // Multi Signatures
        if ($this->buffer->remaining() && $this->beginningMultiSignature($this->buffer)) {
            $this->buffer->skip(1);
            $signaturesSerialized = $this->buffer->readHex($this->buffer->remaining() * 2);
            $data['signatures']   = [];

            $moreSignatures = true;
            while ($moreSignatures) {
                $mLength = intval(substr($signaturesSerialized, 2, 2), 16);

                if ($mLength > 0) {
                    $data['signatures'][] = substr($signaturesSerialized, 0, ($mLength + 2) * 2);
                } else {
                    $moreSignatures = false;
                }

                $signaturesSerialized = substr($signaturesSerialized, ($mLength + 2) * 2);
            }
        }

        if ($this->buffer->remaining()) {
            throw new \Exception('signature buffer not exhausted');
        }
    }

    private function currentSignatureLength(ByteBuffer $buffer)
    {
        $mark = $buffer->current();

        $lengthHex = $buffer->skip(1)->readHex(1 * 2);

        $buffer->position($mark);

        return intval($lengthHex, 16) + 2;
    }

    private function beginningMultiSignature(ByteBuffer $buffer)
    {
        $mark = $buffer->current();

        $marker = $buffer->readUint8();

        $buffer->position($mark);

        return $marker === 255;
    }

    private function detectSchnorr(): bool
    {
        $remaining = $this->buffer->remaining();

        // `signature` / `secondSignature`
        if ($remaining === 64 || $remaining === 128) {
            return true;
        }

        // `signatures` of a multi signature transaction (type != 4)
        if ($remaining % 65 === 0) {
            return true;
        }

        // only possiblity left is a type 4 transaction with and without a `secondSignature`.
        if (($remaining - 64) % 65 === 0 || ($remaining - 128) % 65 === 0) {
            return true;
        }

        return false;
    }

    /**
     * Handle the deserialization of transaction data with a version of 2.0.
     *
     * @param Transaction $transaction
     *
     * @return Transaction
     */
    public function handleVersionTwo(Transaction $transaction): Transaction
    {
        $transaction->data['id'] = Hash::sha256(Serializer::new($transaction)->serialize())->getHex();

        return $transaction;
    }
}
