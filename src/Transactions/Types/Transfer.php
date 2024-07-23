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

namespace ArkEcosystem\Crypto\Transactions\Types;

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;
use ArkEcosystem\Crypto\Utils\Address;

/**
 * This is the serializer class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class Transfer extends Transaction
{
    public function serialize(array $options = []): ByteBuffer
    {
        $buffer = ByteBuffer::new(24);

        $buffer->writeUInt64(+$this->data['amount']);

        $buffer->writeUInt32($this->data['expiration'] ?? 0);

        $buffer->writeHex(Address::toBufferHexString($this->data['recipientId']));

        return $buffer;
    }

    public function deserialize(ByteBuffer $buffer): void
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
