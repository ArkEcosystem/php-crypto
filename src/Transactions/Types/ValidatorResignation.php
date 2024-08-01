<?php

declare(strict_types=1);



namespace ArkEcosystem\Crypto\Transactions\Types;

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;


class ValidatorResignation extends Transaction
{
    public function serializeData(array $options = []): ByteBuffer
    {
        return ByteBuffer::new(0);
    }

    public function deserializeData(ByteBuffer $buffer): void
    {
    }
}
