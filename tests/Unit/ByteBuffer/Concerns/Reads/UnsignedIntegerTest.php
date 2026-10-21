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

it('should read uint8', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeUInt8(8);
    $buffer->position(0);

    $this->assertSame(8, $buffer->readUInt8());
});

it('should read uint16', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeUInt16(16);
    $buffer->position(0);

    $this->assertSame(16, $buffer->readUInt16());
});

it('should read uint32', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeUInt32(32);
    $buffer->position(0);

    $this->assertSame(32, $buffer->readUInt32());
});

it('should read uint64', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeUInt64(64);
    $buffer->position(0);

    $this->assertSame(64, $buffer->readUInt64());
});

it('should read ubyte', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeUByte(8);
    $buffer->position(0);

    $this->assertSame(8, $buffer->readUByte());
});

it('should read ushort', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeUShort(15);
    $buffer->position(0);

    $this->assertSame(15, $buffer->readUShort());
});

it('should read uint', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeUInt(32);
    $buffer->position(0);

    $this->assertSame(32, $buffer->readUInt());
});

it('should read ulong', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeULong(64);
    $buffer->position(0);

    $this->assertSame(64, $buffer->readULong());
});
