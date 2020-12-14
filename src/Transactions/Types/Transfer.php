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

use BitWasp\Bitcoin\Base58;
use BitWasp\Buffertools\Buffer;
use Konceiver\ByteBuffer\ByteBuffer;

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
        $buffer->writeHex(Base58::decodeCheck($this->data['recipientId'])->getHex());

        return $buffer;
    }

    public function deserialize(ByteBuffer $buffer): void
    {
        $this->data['amount']      = strval($buffer->readUInt64());
        $this->data['expiration']  = $buffer->readUInt32();
        $this->data['recipientId'] = Base58::encodeCheck(new Buffer(hex2bin($buffer->readHex(21 * 2))));
    }

    public function hasVendorField(): bool
    {
        return true;
    }
}
