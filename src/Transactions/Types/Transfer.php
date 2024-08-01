<?php

declare(strict_types=1);



namespace ArkEcosystem\Crypto\Transactions\Types;

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;
use ArkEcosystem\Crypto\Utils\Address;


class Transfer extends Transaction
{
    public function serializeData(array $options = []): ByteBuffer
    {
        $buffer = ByteBuffer::new(24);

        $buffer->writeUInt64(+$this->data['amount']);

        $buffer->writeUInt32($this->data['expiration'] ?? 0);

        $buffer->writeHex(Address::toBufferHexString($this->data['recipientId']));

        return $buffer;
    }

    public function deserializeData(ByteBuffer $buffer): void
    {
        $this->data['amount']      = strval($buffer->readUInt64());

        $this->data['expiration']  = $buffer->readUInt32();

        $this->data['recipientId'] = Address::fromByteBuffer($buffer);
    }

    public function hasVendorField(): bool
    {
        return true;
    }
}
