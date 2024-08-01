<?php

declare(strict_types=1);



namespace ArkEcosystem\Crypto\Transactions\Types;

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;

/**
 * This is the serializer class.
 */
class UsernameRegistration extends Transaction
{
    public function serializeData(array $options = []): ByteBuffer
    {
        $buffer = ByteBuffer::new(1);

        $username = $this->data['asset']['username'];

        $buffer->writeUint8(strlen($username));
        $buffer->writeString($username);

        return $buffer;
    }

    public function deserializeData(ByteBuffer $buffer): void
    {
        $usernameLength = $buffer->readUint8();

        $this->data['asset'] = [
            'username' => $buffer->readString($usernameLength),
        ];
    }
}
