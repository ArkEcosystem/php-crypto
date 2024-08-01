<?php

declare(strict_types=1);



namespace ArkEcosystem\Crypto\Transactions\Types;

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;


class Vote extends Transaction
{
    public function serializeData(array $options = []): ByteBuffer
    {
        $buffer = ByteBuffer::new(24);

        $votes   = array_key_exists('votes', $this->data['asset']) ? $this->data['asset']['votes'] : [];
        $unvotes = array_key_exists('unvotes', $this->data['asset']) ? $this->data['asset']['unvotes'] : [];

        $buffer->writeUInt8(count($votes));
        $buffer->writeHex(implode('', $votes));

        $buffer->writeUInt8(count($unvotes));
        $buffer->writeHex(implode('', $unvotes));

        return $buffer;
    }

    public function deserializeData(ByteBuffer $buffer): void
    {
        $voteLength = $buffer->readUInt8();

        if ($voteLength > 0) {
            $this->data['asset']['votes'] = [];

            for ($i = 0; $i < $voteLength; $i++) {
                $vote = $buffer->readHex(66);

                $this->data['asset']['votes'][] = $vote;
            }
        }

        $unvoteLength = $buffer->readUInt8();

        if ($unvoteLength > 0) {
            $this->data['asset']['unvotes'] = [];

            for ($i = 0; $i < $unvoteLength; $i++) {
                $unvote = $buffer->readHex(66);

                $this->data['asset']['unvotes'][] = $unvote;
            }
        }
    }
}
