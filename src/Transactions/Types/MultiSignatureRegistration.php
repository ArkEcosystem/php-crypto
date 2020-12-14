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
class MultiSignatureRegistration extends Transaction
{
    public function serialize(array $options = []): ByteBuffer
    {
        $buffer = ByteBuffer::new(1);
        if ($this->data['version'] === 2) {
            $publicKeysLength = count($this->data['asset']['multiSignature']['publicKeys']);
            $buffer           = ByteBuffer::new(2 + 33 * $publicKeysLength);

            $buffer->writeUInt8($this->data['asset']['multiSignature']['min']);
            $buffer->writeUInt8($publicKeysLength);

            foreach ($this->data['asset']['multiSignature']['publicKeys'] as $publicKey) {
                $buffer->writeHex($publicKey);
            }
        } else {
            // legacy
            $keysgroup = [];
            foreach ($this->data['asset']['multiSignatureLegacy']['keysgroup'] as $key) {
                $keysgroup[] = '+' === substr($key, 0, 1)
                    ? substr($key, 1)
                    : $key;
            }
            $buffer->writeUInt8($this->data['asset']['multiSignatureLegacy']['min']);
            $buffer->writeUInt8(count($this->data['asset']['multiSignatureLegacy']['keysgroup']));
            $buffer->writeUInt8($this->data['asset']['multiSignatureLegacy']['lifetime']);
            $buffer->writeHex(implode('', $keysgroup));
        }

        return $buffer;
    }

    public function deserialize(ByteBuffer $buffer): void
    {
        if ($this->data['version'] === 2) {
            $this->data['asset'] = [
                'multiSignature' => [
                    'min'        => $buffer->readUInt8(),
                    'publicKeys' => [],
                ],
            ];

            $count = $buffer->readUInt8();
            for ($i = 0; $i < $count; $i++) {
                $this->data['asset']['multiSignature']['publicKeys'][] = $buffer->readHex(33 * 2);
            }
        } else {
            // legacy
            $min            = $buffer->readUInt8();
            $keysgroupCount = $buffer->readUInt8();
            $lifetime       = $buffer->readUInt8();

            $this->data->asset = [
                'multiSignatureLegacy' => [
                    'min'       => $min,
                    'lifetime'  => $lifetime,
                    'keysgroup' => [],
                ],
            ];

            for ($i = 0; $i < $keysgroupCount; $i++) {
                $this->data->asset['multiSignatureLegacy']['keysgroup'][] = $buffer->readHex(33 * 2);
            }
        }
    }
}
