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
class DelegateResignation extends Transaction
{
    public function serialize(array $options = []): ByteBuffer
    {
        return ByteBuffer::new(0);
    }

    public function deserialize(ByteBuffer $buffer): void
    {
    }
}
