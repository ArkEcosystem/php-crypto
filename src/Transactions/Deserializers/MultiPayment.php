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

namespace ArkEcosystem\Crypto\Transactions\Deserializers;

use BitWasp\Bitcoin\Base58;
use BitWasp\Buffertools\Buffer;

/**
 * This is the deserializer class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class MultiPayment extends AbstractDeserializer
{
    /**
     * Handle the deserialisation of "multi payment" data.
     *
     * @return object
     */
    public function deserialize(): object
    {
        $this->buffer->position($this->assetOffset / 2);

        $this->transaction->asset = ['payments' => []];

        $count  = $this->buffer->readUInt16() & 0xff;
        $offset = $this->assetOffset / 2 + 1;

        for ($i = 0; $i < $count; ++$i) {
            $this->transaction->asset['payments'][] = [
                'amount'      => $this->buffer->readUInt64(),
                'recipientId' => Base58::encodeCheck(new Buffer(hex2bin($this->buffer->readHex(21)))),
            ];

            $offset += 22;
        }

        $this->transaction->amount = array_sum(array_column($this->transaction->asset['payments'], 'amount'));

        return $this->parseSignatures($offset * 2);
    }
}
