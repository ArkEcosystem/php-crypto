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
class MultiPayment extends Transaction
{
    /**
     * Handle the serialization of "multi payment" data.
     *
     * @return string
     */
    public function serialize(array $options = []): ByteBuffer
    {
        $buffer = ByteBuffer::new(1); // initialize with size 1, will expand as we add bytes
        $buffer->writeUInt16(count($this->data['asset']['payments']));

        foreach ($this->data['asset']['payments'] as $payment) {
            $buffer->writeUInt64(+$payment['amount']);
            $buffer->append(Base58::decodeCheck($payment['recipientId'])->getBinary());
        }

        return $buffer;
    }

    public function deserialize(ByteBuffer $buffer): void
    {
        $this->data['asset'] = ['payments' => []];

        $count = $buffer->readUInt16();

        for ($i = 0; $i < $count; $i++) {
            $this->data['asset']['payments'][] = [
                'amount'      => strval($buffer->readUInt64()),
                'recipientId' => Base58::encodeCheck(new Buffer(hex2bin($buffer->readHex(21 * 2)))),
            ];
        }

        $this->data['amount'] = '0';
    }

    public function hasVendorField(): bool
    {
        return true;
    }
}
