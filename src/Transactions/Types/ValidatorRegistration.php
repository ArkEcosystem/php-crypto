<?php

declare(strict_types=1);



namespace ArkEcosystem\Crypto\Transactions\Types;

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;


class ValidatorRegistration extends Transaction
{
    public function serializeData(array $options = []): ByteBuffer
    {
        $buffer = ByteBuffer::new(1);
        $buffer->writeHex($this->data['asset']['validatorPublicKey']);

        return $buffer;
    }

    public function deserializeData(ByteBuffer $buffer): void
    {
        $this->data['asset'] = [
            'validatorPublicKey' => $buffer->readHex(48 * 2),
        ];
    }
}
