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
class UsernameResignation extends Transaction
{
    public function serializeData(array $options = []): ByteBuffer
    {
        return ByteBuffer::new(0);
    }

    public function deserializeData(ByteBuffer $buffer): void
    {
    }
}
