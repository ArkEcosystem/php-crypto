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

/**
 * This is the deserializer class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class Vote extends AbstractDeserializer
{
    /**
     * Handle the deserialization of "second signature registration" data.
     *
     * @return object
     */
    public function deserialize(): object
    {
        $this->buffer->position($this->assetOffset / 2);

        $voteLength = $this->buffer->readUInt8() & 0xff;

        $this->transaction->asset = ['votes' => []];

        $vote = null;
        for ($i = 0; $i < $voteLength; $i++) {
            $this->buffer->position($this->assetOffset + 2 + $i * 2 * 34);

            $vote = $this->buffer->readHexRaw(34 * 2);
            $vote = ('1' === $vote[1] ? '+' : '-').substr($vote, 2);
            $this->transaction->asset['votes'][] = $vote;
        }

        return $this->parseSignatures($this->assetOffset + 2 + $voteLength * 34 * 2);
    }
}
