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

namespace ArkEcosystem\Tests\Crypto\Concerns\Writes;

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;

it('should write int8', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeInt8(8);

    $this->assertSame(1, $buffer->internalSize());
});

it('should write int16', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeInt16(16);

    $this->assertSame(2, $buffer->internalSize());
});

it('should write int32', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeInt32(32);

    $this->assertSame(4, $buffer->internalSize());
});

it('should write int64', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeInt64(64);

    $this->assertSame(8, $buffer->internalSize());
});

it('should write byte', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeByte(8);

    $this->assertSame(1, $buffer->internalSize());
});

it('should write short', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeShort(16);

    $this->assertSame(2, $buffer->internalSize());
});

it('should write int', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeInt(32);

    $this->assertSame(4, $buffer->internalSize());
});

it('should write long', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeLong(64);

    $this->assertSame(8, $buffer->internalSize());
});
