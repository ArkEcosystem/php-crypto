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

use Konceiver\ByteBuffer\ByteBuffer;

class HtlcClaim extends Transaction
{
    public function serialize(array $options = []): ByteBuffer
    {
        $lockBuffer = ByteBuffer::fromHex($this->data['asset']['claim']['lockTransactionId']);

        $buffer = ByteBuffer::new(1);
        $buffer->writeString($this->data['asset']['claim']['unlockSecret']);
        $buffer->prepend($lockBuffer);

        return $buffer;
    }

    public function deserialize(ByteBuffer $buffer): void
    {
        $lockTransactionId = $buffer->readHex(32 * 2);
        $unlockSecret      = $buffer->readString(32);

        $this->data['asset'] = [
            'claim' => [
                'lockTransactionId' => $lockTransactionId,
                'unlockSecret'      => $unlockSecret,
            ],
        ];
    }
}
