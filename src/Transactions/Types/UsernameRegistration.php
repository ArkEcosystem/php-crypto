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
 */
class UsernameRegistration extends Transaction
{
    public function serialize(array $options = []): ByteBuffer
    {
        $buffer = ByteBuffer::new(1);

        $username = $this->data['asset']['username'];

        $buffer->writeUint8(strlen($username));
        $buffer->writeString($username);

        return $buffer;
    }

    public function deserialize(ByteBuffer $buffer): void
    {
        $usernameLength = $buffer->readUint8();

        $this->data['asset'] = [
            'username' => $buffer->readString($usernameLength),
        ];
    }
}
