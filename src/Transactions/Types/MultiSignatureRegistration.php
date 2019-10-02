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

use BrianFaust\ByteBuffer\ByteBuffer;

/**
 * This is the serializer class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class MultiSignatureRegistration extends Transaction
{
    public function serialize(array $options = []): ByteBuffer
    {
        $publicKeysLength = count($this->data['asset']['multiSignature']['publicKeys']);
        $buffer = ByteBuffer::new(2 + 33 * $publicKeysLength);

        $buffer->writeUInt8($this->data['asset']['multiSignature']['min']);
        $buffer->writeUInt8($publicKeysLength);

        foreach ($this->data['asset']['multiSignature']['publicKeys'] as $publicKey) {
            $buffer->writeHex($publicKey);
        }

        return $buffer;
    }

    public function deserialize(ByteBuffer $buffer): void
    {
        $this->data['asset'] = [
            'multiSignature' => [
                'min'      => $buffer->readUInt8(),
                'publicKeys' => [],
            ],
        ];

        $count = $buffer->readUInt8();
        for ($i = 0; $i < $count; $i++) {
            $this->data['asset']['multiSignature']['publicKeys'][] = $buffer->readHex(33 * 2);
        }
    }
}
