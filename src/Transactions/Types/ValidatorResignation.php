<?php

declare(strict_types=1);



namespace ArkEcosystem\Crypto\Transactions\Types;

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;

/**
 * This is the serializer class.
 *
 * @author Brian Faust <brian@ark.io>
 */
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
