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
class ValidatorRegistration extends Transaction
{
    public function serialize(array $options = []): ByteBuffer
    {
        $buffer = ByteBuffer::new(1);
        $buffer->writeHex($this->data['asset']['validatorPublicKey']);
        
        return $buffer;
    }

    public function deserialize(ByteBuffer $buffer): void
    {
        $this->data['asset'] = [
            'validatorPublicKey' => $buffer->readHex(48 * 2),
        ];
    }
}
