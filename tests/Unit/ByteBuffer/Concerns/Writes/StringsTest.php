<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;

/*
 * @covers \ArkEcosystem\Crypto\ByteBuffer\Concerns\Writes\Strings
 */

it('should write bytes', function () {
    $buffer = ByteBuffer::new(0);
    $buffer->writeBytes('Hello World');

    expect($buffer->internalSize())->toBe(11);
});

it('should write string', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeString('Hello World');

    expect($buffer->internalSize())->toBe(11);
});

it('should write utf8 string', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeUTF8String('Hello World 😄');

    expect($buffer->internalSize())->toBe(20);
});

it('should write c string', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeCString('Hello World');

    expect($buffer->internalSize())->toBe(12);
    expect($buffer->toHex())->toBe('48656c6c6f20576f726c6400');
});

it('should write i string', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeIString('Hello World');

    expect($buffer->internalSize())->toBe(15);
    expect($buffer->toHex())->toBe('0000000b48656c6c6f20576f726c64');
});

it('should write v string', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeVString('Hello World');

    expect($buffer->internalSize())->toBe(12);
    expect($buffer->toHex())->toBe('0b48656c6c6f20576f726c64');
});
