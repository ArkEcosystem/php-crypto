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

/**
 * This is the serializer class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class Vote extends Transaction
{
    public function serialize(array $options = []): ByteBuffer
    {
        $buffer = ByteBuffer::new(24);

        $voteBytes = [];

        foreach ($this->data['asset']['votes'] as $vote) {
            $voteBytes[] = '+' === substr($vote, 0, 1)
                ? '01'.substr($vote, 1)
                : '00'.substr($vote, 1);
        }

        $buffer->writeUInt8(count($this->data['asset']['votes']));
        $buffer->writeHex(implode('', $voteBytes));

        return $buffer;
    }

    public function deserialize(ByteBuffer $buffer): void
    {
        $voteLength = $buffer->readUInt8();

        $this->data['asset'] = ['votes' => []];

        $vote = null;
        for ($i = 0; $i < $voteLength; $i++) {
            $vote                           = $buffer->readHex(34 * 2);
            $vote                           = ('1' === $vote[1] ? '+' : '-').substr($vote, 2);
            $this->data['asset']['votes'][] = $vote;
        }
    }
}
