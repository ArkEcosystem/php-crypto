<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions;

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;
use ArkEcosystem\Crypto\Configuration\Network;
use ArkEcosystem\Crypto\Transactions\Types\AbstractTransaction;
use ArkEcosystem\Crypto\Utils\Address;
use ArkEcosystem\Crypto\Utils\RlpEncoder;
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
     * @return Buffer
     */
    public function serialize(bool $skipSignature = false): Buffer
    {
        $fields = [
            self::toBeArray($this->transaction->data['network']),
            self::toBeArray($this->transaction->data['nonce']),
            self::toBeArray(0),
            self::toBeArray($this->transaction->data['gasPrice']),
            self::toBeArray($this->transaction->data['gasLimit']),
            $this->transaction->data['recipientAddress'] ?? '0x',
            self::toBeArray($this->transaction->data['value']),
            $this->transaction->data['data'],
            [],
        ];

        if (
            isset($this->transaction->data['v'], $this->transaction->data['r'], $this->transaction->data['s']) &&
            ! $skipSignature
        ) {
            $fields[] = self::toBeArray($this->transaction->data['v'] - 27);
            $fields[] = '0x'.$this->transaction->data['r'];
            $fields[] = '0x'.$this->transaction->data['s'];
        }

        $rlpEncoded = RlpEncoder::encode($fields);
        $serialized = '02'.substr($rlpEncoded, 2);

        $this->transaction->serialized = new Buffer(hex2bin($serialized));

        return $this->transaction->serialized;
    }

    private static function toBeArray(mixed $value): string
    {
        if (is_int($value)) {
            if ($value === 0) {
                return '0x';
            }

            return '0x'.dechex($value);
        }

        if (is_string($value)) {
            if (str_starts_with($value, '0x')) {
                return $value;
            }

            return '0x'.dechex((int) $value);
        }

        return '0x';
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
