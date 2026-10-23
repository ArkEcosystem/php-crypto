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

it('should read int8', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeInt8(8);
    $buffer->position(0);

    $this->assertSame(8, $buffer->readInt8());
});

it('should read int16', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeInt16(16);
    $buffer->position(0);

    $this->assertSame(16, $buffer->readInt16());
});

it('should read int32', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeInt32(32);
    $buffer->position(0);

    $this->assertSame(32, $buffer->readInt32());
});

it('should read int64', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeInt64(64);
    $buffer->position(0);

    $this->assertSame(64, $buffer->readInt64());
});

it('should read byte', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeByte(8);
    $buffer->position(0);

    $this->assertSame(8, $buffer->readByte());
});

it('should read short', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeShort(16);
    $buffer->position(0);

    $this->assertSame(16, $buffer->readShort());
});

it('should read int', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeInt(32);
    $buffer->position(0);

    $this->assertSame(32, $buffer->readInt());
});

it('should read long', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeLong(64);
    $buffer->position(0);

    $this->assertSame(64, $buffer->readLong());
});
