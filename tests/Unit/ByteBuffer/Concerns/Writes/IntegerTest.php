<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;

/**
 * @covers \ArkEcosystem\Crypto\ByteBuffer\Concerns\Writes\Integer
 */

it('should write int8', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeInt8(8);

    expect($buffer->internalSize())->toBe(1);
});

it('should write int16', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeInt16(16);

    expect($buffer->internalSize())->toBe(2);
});

it('should write int32', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeInt32(32);

    expect($buffer->internalSize())->toBe(4);
});

it('should write int64', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeInt64(64);

    expect($buffer->internalSize())->toBe(8);
});

it('should write byte', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeByte(8);

    expect($buffer->internalSize())->toBe(1);
});

it('should write short', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeShort(16);

    expect($buffer->internalSize())->toBe(2);
});

it('should write int', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeInt(32);

    expect($buffer->internalSize())->toBe(4);
});

it('should write long', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeLong(64);

    expect($buffer->internalSize())->toBe(8);
});
