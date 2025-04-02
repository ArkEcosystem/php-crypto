<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;

/**
 * @covers \ArkEcosystem\Crypto\ByteBuffer\Concerns\Writes\UnsignedInteger
 */

it('should write uint8', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeUInt8(8);

    expect($buffer->internalSize())->toBe(1);
});

it('should write uint16', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeUInt16(16);

    expect($buffer->internalSize())->toBe(2);
});

it('should write uint32', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeUInt32(32);

    expect($buffer->internalSize())->toBe(4);
});

it('should write uint64', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeUInt64(64);

    expect($buffer->internalSize())->toBe(8);
});

it('should write ubyte', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeUByte(8);

    expect($buffer->internalSize())->toBe(1);
});

it('should write ushort', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeUShort(15);

    expect($buffer->internalSize())->toBe(2);
});

it('should write uint', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeUInt(32);

    expect($buffer->internalSize())->toBe(4);
});

it('should write ulong', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeULong(64);

    expect($buffer->internalSize())->toBe(8);
});
