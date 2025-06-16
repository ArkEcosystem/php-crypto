<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;

/*
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

test('it should write uint256', function () {
    // 256-bit unsigned integer (32 bytes)
    $value  = '1157920892373161954235709850086879078532699846656405640323232344'; // max uint256
    $buffer = ByteBuffer::new(0);
    $buffer->writeUInt256($value);

    expect($buffer->internalSize())->toBe(32);
});

test('it should write uint256 gmp value', function () {
    // 256-bit unsigned integer (32 bytes)
    $value  = gmp_init('1157920892373161954235709850086879078532699846656405640323232344'); // max uint256
    $buffer = ByteBuffer::new(0);
    $buffer->writeUInt256($value);

    expect($buffer->internalSize())->toBe(32);
});

test('it should throw exception when writing invalid uint256', function () {
    // 256-bit unsigned integer (32 bytes)
    $value  = 'asd';
    $buffer = ByteBuffer::new(0);
    $buffer->writeUInt256($value);
})->throws(InvalidArgumentException::class, 'The value must be a numeric string, integer, or GMP object.');

test('it should throw exception when writing uint256 which is too long', function () {
    // 256-bit unsigned integer (32 bytes)
    $value  = '1157920892373161954235709850086879078532699846656405640323232344444411579208923731619542357098500868790785326998466564056403232323444444';
    $buffer = ByteBuffer::new(0);
    $buffer->writeUInt256($value);
})->throws(InvalidArgumentException::class, 'The value must fit into 256 bits.');
