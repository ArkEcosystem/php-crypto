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

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;

/**
 * This is the serializer class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class MultiSignatureRegistration extends Transaction
{
    public function serializeData(array $options = []): ByteBuffer
    {
        $buffer = ByteBuffer::new(1);

        $buffer->writeUInt8($this->data['asset']['multiSignature']['min']);
        $buffer->writeUInt8(count($this->data['asset']['multiSignature']['publicKeys']));

        foreach ($this->data['asset']['multiSignature']['publicKeys'] as $publicKey) {
            $buffer->writeHex($publicKey);
        }

        return $buffer;
    }

    public function deserializeData(ByteBuffer $buffer): void
    {
        $asset = [
            'multiSignature' => [
                'min'        => $buffer->readUInt8(),
                'publicKeys' => [],
            ],
        ];

        $publicKeysCount = $buffer->readUInt8();

        for ($i = 0; $i < $publicKeysCount; $i++) {
            $asset['multiSignature']['publicKeys'][] = $buffer->readHex(33 * 2);
        }

        $this->data['asset'] = $asset;
    }
}
