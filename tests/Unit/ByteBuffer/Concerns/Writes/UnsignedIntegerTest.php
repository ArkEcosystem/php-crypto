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

it('should write uint8', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeUInt8(8);

    $this->assertSame(1, $buffer->internalSize());
});

it('should write uint16', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeUInt16(16);

    $this->assertSame(2, $buffer->internalSize());
});

it('should write uint32', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeUInt32(32);

    $this->assertSame(4, $buffer->internalSize());
});

it('should write uint64', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeUInt64(64);

    $this->assertSame(8, $buffer->internalSize());
});

it('should write ubyte', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeUByte(8);

    $this->assertSame(1, $buffer->internalSize());
});

it('should write ushort', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeUShort(15);

    $this->assertSame(2, $buffer->internalSize());
});

it('should write uint', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeUInt(32);

    $this->assertSame(4, $buffer->internalSize());
});

it('should write ulong', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeULong(64);

    $this->assertSame(8, $buffer->internalSize());
});
