<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;

/**
 * @covers \ArkEcosystem\Crypto\ByteBuffer\Concerns\Reads\Integer
 */

test('it should read int8', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeInt8(8);
    $buffer->position(0);

    expect($buffer->readInt8())->toBe(8);
});

test('it should read int16', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeInt16(16);
    $buffer->position(0);

    expect($buffer->readInt16())->toBe(16);
});

test('it should read int32', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeInt32(32);
    $buffer->position(0);

    expect($buffer->readInt32())->toBe(32);
});

test('it should read int64', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeInt64(64);
    $buffer->position(0);

    expect($buffer->readInt64())->toBe(64);
});

test('it should read byte', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeByte(8);
    $buffer->position(0);

    expect($buffer->readByte())->toBe(8);
});

test('it should read short', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeShort(16);
    $buffer->position(0);

    expect($buffer->readShort())->toBe(16);
});

test('it should read int', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeInt(32);
    $buffer->position(0);

    expect($buffer->readInt())->toBe(32);
});

test('it should read long', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeLong(64);
    $buffer->position(0);

    expect($buffer->readLong())->toBe(64);
});
