<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions;

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;
use ArkEcosystem\Crypto\Configuration\Network;
use ArkEcosystem\Crypto\Transactions\Types\AbstractTransaction;
use ArkEcosystem\Crypto\Utils\Address;
use BitWasp\Buffertools\Buffer;

class Serializer
{
    public AbstractTransaction $transaction;

    /**
     * Create a new serializer instance.
     *
     * @param AbstractTransaction $transaction
     */
    private function __construct($transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Create a new deserializer instance.
     *
     * @param AbstractTransaction $transaction
     */
    public static function new($transaction)
    {
        return new static($transaction);
    }

    public static function getBytes(AbstractTransaction $transaction, bool $skipSignature = false): Buffer
    {
        return $transaction->serialize(skipSignature: $skipSignature);
    }

    /**
     * Perform AIP11 compliant serialization.
     *
     * @return Buffer
     */
    public function serialize(bool $skipSignature = false): Buffer
    {
        $buffer = ByteBuffer::new(0); // initialize with size 0, size will expand as we add bytes

        $this->serializeCommon($buffer);

        $this->serializeData($buffer);

        $this->serializeSignatures($buffer, $skipSignature);

        return new Buffer($buffer->toString('binary'));
    }

    private function serializeData(ByteBuffer $buffer): void
    {
        $buffer->writeUint256($this->transaction->data['value']);

        if (isset($this->transaction->data['recipientAddress'])) {
            $buffer->writeUInt8(1); // Recipient marker

            $buffer->writeHex(
                Address::toBufferHexString($this->transaction->data['recipientAddress'])
            );
        } else {
            $buffer->writeUInt8(0); // No recipient
        }

        $payloadHex    = $this->transaction->data['data'] ?? '';

        $payloadLength = strlen($payloadHex);

        $buffer->writeUInt32($payloadLength / 2);

        // Write payload as hex
        $buffer->writeHex($payloadHex);
    }

    /**
     * Handle the serialization of transaction data.
     *
     * @return string
     */
    private function serializeSignatures(ByteBuffer $buffer, bool $skipSignature = false): void
    {
        if (! $skipSignature && isset($this->transaction->data['signature'])) {
            $buffer->writeHex($this->transaction->data['signature']);
        }
    }

    private function serializeCommon(ByteBuffer $buffer): void
    {
        $buffer->writeUInt8($this->transaction->data['network'] ?? Network::version());
        $buffer->writeUint64(+$this->transaction->data['nonce']);
        $buffer->writeUint32($this->transaction->data['gasPrice']);
        $buffer->writeUint32($this->transaction->data['gasLimit']);
    }
}
