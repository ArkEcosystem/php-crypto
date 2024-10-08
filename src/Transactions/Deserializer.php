<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions;

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;
use ArkEcosystem\Crypto\Enums\Types;
use ArkEcosystem\Crypto\Transactions\Types\Transaction;
use BitWasp\Bitcoin\Crypto\Hash;

class Deserializer
{
    private ByteBuffer $buffer;

    /**
     * Create a new deserializer instance.
     *
     * @param  object  $serialized
     */
    public function __construct(string $serialized)
    {
        $this->buffer = strpos($serialized, "\0") === false
            ? ByteBuffer::fromHex($serialized)
            : ByteBuffer::fromBinary($serialized);
    }

    /**
     * Create a new deserializer instance.
     */
    public static function new(string $serialized)
    {
        return new static($serialized);
    }

    /**
     * Perform AIP11 compliant deserialization.
     */
    public function deserialize(): Transaction
    {
        $data = [];

        $this->deserializeCommon($data);

        $transactionClass  = Types::fromValue($data['type'])->transactionClass();
        $transaction       = new $transactionClass();
        $transaction->data = $data;

        $this->deserializeVendorField($transaction);

        // Deserialize type specific parts
        $transaction->deserializeData($this->buffer);

        $this->deserializeSignatures($transaction->data);

        if (! isset($transaction->data['amount'])) {
            $transaction->data['amount'] = '0';
        }

        $transaction->data['id'] = Hash::sha256($transaction->serialize())->getHex();

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

        if ($data['type'] === Types::EVM_CALL) {
            $data['fee']             = $this->buffer->readUInt256();
        } else {
            $data['fee']             = strval($this->buffer->readUInt64());
        }
    }

    private function deserializeVendorField(Transaction $transaction): void
    {
        $vendorFieldLength = $this->buffer->readUInt8();
        if ($vendorFieldLength > 0) {
            if ($transaction->hasVendorField()) {
                $transaction->data['vendorField'] = $this->buffer->readHexString($vendorFieldLength * 2);
            } else {
                $this->buffer->skip($vendorFieldLength);
            }
        }
    }

    private function deserializeSignatures(array &$data): void
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
}
