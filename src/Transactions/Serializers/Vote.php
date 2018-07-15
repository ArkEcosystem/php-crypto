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

namespace ArkEcosystem\Crypto\Transactions\Serializers;

/**
 * This is the serializer class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class Vote extends AbstractSerializer
{
    /**
     * Handle the serialisation of "second signature registration" data.
     *
     * @return string
     */
    public function serialize(): void
    {
        $voteBytes = [];

        foreach ($this->transaction['asset']['votes'] as $vote) {
            $voteBytes[] = '+' === substr($vote, 0, 1)
                ? '01'.substr($vote, 1)
                : '00'.substr($vote, 1);
        }

        $this->buffer->writeUInt8(count($this->transaction['asset']['votes']));
        $this->buffer->writeHexBytes(implode('', $voteBytes));
    }
}
