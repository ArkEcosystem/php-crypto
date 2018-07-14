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

namespace ArkEcosystem\Crypto\Deserializers;

/**
 * This is the deserializer class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class IPFS extends AbstractDeserializer
{
    /**
     * Handle the deserialisation of "ipfs" data.
     *
     * @return object
     */
    public function deserialize(): object
    {
        $this->buffer->position($this->assetOffset / 2);

        $dagLength = $this->buffer->readUInt8() & 0xff;

        $this->transaction->asset = [
            'dag' => $this->buffer->position($this->assetOffset + 2)->readHexBytes($dagLength * 2),
        ];

        return $this->parseSignatures($this->assetOffset + 2 + $dagLength * 2);
    }
}
