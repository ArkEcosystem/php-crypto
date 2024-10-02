<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions\Types;

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;
use ArkEcosystem\Crypto\Utils\Address;

class EvmCall extends Transaction
{
    /**
     * Serialize the EVM call transaction data.
     *
     * @param array $options
     * @return ByteBuffer
     */
    public function serializeData(array $options = []): ByteBuffer
    {
        $buffer = ByteBuffer::new(0);

        // Write amount (uint64)
        $buffer->writeUInt64((int) $this->data['amount']);

        // Write recipient marker and recipientId (if present)
        if (isset($this->data['recipientId'])) {
            $buffer->writeUInt8(1); // Recipient marker
            $buffer->writeHex(
                Address::toBufferHexString($this->data['recipientId'])
            );
        } else {
            $buffer->writeUInt8(0); // No recipient
        }

        // Write gasLimit (uint32)
        $buffer->writeUInt32($this->data['asset']['evmCall']['gasLimit']);

        // Write payload length (uint32) and payload
        $payloadHex = $this->data['asset']['evmCall']['payload'];
        $payloadLength = strlen($payloadHex) / 2; // Length in bytes

        $buffer->writeUInt32($payloadLength);

        // Write payload as hex
        $buffer->writeHex($payloadHex);

        return $buffer;
    }

    /**
     * Deserialize the EVM call transaction data.
     *
     * @param ByteBuffer $buffer
     */
    public function deserializeData(ByteBuffer $buffer): void
    {
        // Read amount (uint64)
        $this->data['amount'] = strval($buffer->readUInt64());

        // Read recipient marker and recipientId
        $recipientMarker = $buffer->readUInt8();
        if ($recipientMarker === 1) {
            $this->data['recipientId'] = Address::fromByteBuffer($buffer);
        }

        // Read gasLimit (uint32)
        $gasLimit = $buffer->readUInt32();

        // Read payload length (uint32)
        $payloadLength = $buffer->readUInt32();

        // Read payload as hex
        $payloadHex = $buffer->readHex($payloadLength * 2);

        $this->data['asset'] = [
            'evmCall' => [
                'gasLimit' => $gasLimit,
                'payload'  => $payloadHex,
            ],
        ];
    }

    /**
     * Indicates whether the transaction supports vendor fields.
     *
     * @return bool
     */
    public function hasVendorField(): bool
    {
        return false;
    }
}
