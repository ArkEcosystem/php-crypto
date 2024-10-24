<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions;

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;
use ArkEcosystem\Crypto\Configuration\Network;
use ArkEcosystem\Crypto\Enums\TypeGroup;
use ArkEcosystem\Crypto\Transactions\Types\Transaction;
use BitWasp\Buffertools\Buffer;

class Serializer
{
    public Transaction $transaction;

    /**
     * Create a new serializer instance.
     *
     * @param Transaction $transaction
     */
    private function __construct($transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Create a new deserializer instance.
     *
     * @param Transaction $transaction
     */
    public static function new($transaction)
    {
        return new static($transaction);
    }

    public static function getBytes(Transaction $transaction, array $options = []): Buffer
    {
        return $transaction->serialize($options);
    }

    /**
     * Perform AIP11 compliant serialization.
     *
     * @return Buffer
     */
    public function serialize(array $options = []): Buffer
    {
        $buffer = ByteBuffer::new(1); // initialize with size 1, size will expand as we add bytes

        $this->serializeCommon($buffer);

        // Vendor field length from previous transaction serialization
        // Added for compatibility
        $buffer->writeUInt8(0);

        $typeBuffer = $this->transaction->serializeData($options);
        $buffer->append($typeBuffer);

        $this->serializeSignatures($buffer, $options);

        return new Buffer($buffer->toString('binary'));
    }

    /**
     * Handle the serialization of transaction data.
     *
     * @param ByteBuffer $buffer
     *
     * @return string
     */
    public function serializeSignatures(ByteBuffer $buffer, array $options): void
    {
        $skipSignature       = $options['skipSignature'] ?? false;
        $skipSecondSignature = $options['skipSecondSignature'] ?? false;
        $skipMultiSignature  = $options['skipMultiSignature'] ?? false;

        if (! $skipSignature && isset($this->transaction->data['signature'])) {
            $buffer->writeHex($this->transaction->data['signature']);
        }

        if (! $skipSecondSignature) {
            if (isset($this->transaction->data['secondSignature'])) {
                $buffer->writeHex($this->transaction->data['secondSignature']);
            }
        }

        if (! $skipMultiSignature && isset($this->transaction->data['signatures'])) {
            $buffer->writeHex(implode('', $this->transaction->data['signatures']));
        }
    }

    private function serializeCommon(ByteBuffer $buffer): void
    {
        $buffer->writeUInt8(0xff);
        $buffer->writeUInt8($this->transaction->data['version'] ?? 0x01);
        $buffer->writeUInt8($this->transaction->data['network'] ?? Network::version());

        $buffer->writeUint32($this->transaction->data['typeGroup'] ?? TypeGroup::CORE);
        $buffer->writeUint16($this->transaction->data['type']);
        $buffer->writeUint64(+$this->transaction->data['nonce']);

        if ($this->transaction->data['senderPublicKey']) {
            $buffer->writeHex($this->transaction->data['senderPublicKey']);
        }

        $buffer->writeUint256($this->transaction->data['fee']);
    }
}
