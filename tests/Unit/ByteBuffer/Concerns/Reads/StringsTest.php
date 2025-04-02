<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;

/**
 * @covers \ArkEcosystem\Crypto\ByteBuffer\Concerns\Reads\Strings
 */

test('it should read string', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeString('Hello World');
    $buffer->position(0);

    expect($buffer->readString(11))->toBe('Hello World');
});

test('it should read utf8 string', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeUTF8String('Hello World 😄');
    $buffer->position(0);

    expect($buffer->readUTF8String(20))->toBe('Hello World 😄');
});

test('it should read c string', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeCString('Hello World ');
    $buffer->position(0);

    expect($buffer->readCString(11))->toBe('Hello World');
});

test('it should read i string', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeIString('Hello World');
    $buffer->position(0);

    expect($buffer->readIString(11))->toBe('Hello World');
});

test('it should write v string', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeVString('Hello World');
    $buffer->position(0);

    expect($buffer->readVString(11))->toBe('Hello World');
});
