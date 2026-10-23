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

namespace ArkEcosystem\Tests\Crypto\Concerns\Reads;

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;

it('should read float32', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeFloat32(8.0);
    $buffer->position(0);

    $this->assertSame(8.0, $buffer->readFloat32());
});

it('should read float64', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeFloat64(8.0);
    $buffer->position(0);

    $this->assertSame(8.0, $buffer->readFloat64());
});

it('should read double', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeDouble(8.0);
    $buffer->position(0);

    $this->assertSame(8.0, $buffer->readDouble());
});
