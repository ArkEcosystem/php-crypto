<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;

/**
 * @covers \ArkEcosystem\Crypto\ByteBuffer\Concerns\Reads\UnsignedInteger
 */

test('it should read uint8', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeUInt8(8);
    $buffer->position(0);

    expect($buffer->readUInt8())->toBe(8);
});

test('it should read uint16', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeUInt16(16);
    $buffer->position(0);

    expect($buffer->readUInt16())->toBe(16);
});

test('it should read uint32', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeUInt32(32);
    $buffer->position(0);

    expect($buffer->readUInt32())->toBe(32);
});

test('it should read uint64', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeUInt64(64);
    $buffer->position(0);

    expect($buffer->readUInt64())->toBe(64);
});

test('it should read ubyte', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeUByte(8);
    $buffer->position(0);

    expect($buffer->readUByte())->toBe(8);
});

test('it should read ushort', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeUShort(15);
    $buffer->position(0);

    expect($buffer->readUShort())->toBe(15);
});

test('it should read uint', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeUInt(32);
    $buffer->position(0);

    expect($buffer->readUInt())->toBe(32);
});

test('it should read ulong', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeULong(64);
    $buffer->position(0);

    expect($buffer->readULong())->toBe(64);
});
