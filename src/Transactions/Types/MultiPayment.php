<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions\Types;

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;
use ArkEcosystem\Crypto\Utils\Address;

class MultiPayment extends Transaction
{
    /**
     * Handle the serialization of "multi payment" data.
     *
     * @return string
     */
    public function serializeData(array $options = []): ByteBuffer
    {
        $buffer = ByteBuffer::new(1); // initialize with size 1, will expand as we add bytes
        $buffer->writeUInt16(count($this->data['asset']['payments']));

        foreach ($this->data['asset']['payments'] as $payment) {
            $buffer->writeUInt64(+$payment['amount']);
            $buffer->writeHex(
                Address::toBufferHexString($payment['recipientId'])
            );
        }

        return $buffer;
    }

    public function deserializeData(ByteBuffer $buffer): void
    {
        $this->data['asset'] = ['payments' => []];

        $count = $buffer->readUInt16();

        for ($i = 0; $i < $count; $i++) {
            $this->data['asset']['payments'][] = [
                'amount'      => strval($buffer->readUInt64()),
                'recipientId' => Address::fromByteBuffer($buffer),
            ];
        }

        $this->data['amount'] = strval(array_sum(array_column($this->data['asset']['payments'], 'amount')));
    }

    public function hasVendorField(): bool
    {
        return true;
    }
}
