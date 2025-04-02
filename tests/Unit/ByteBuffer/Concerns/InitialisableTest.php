<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;

/**
 * @covers \ArkEcosystem\Crypto\ByteBuffer\Concerns\Initialisable
 */

beforeEach(function () {
    $this->expected = '48656c6c6f20576f726c6420f09f9884';
});

it('should initialise from binary', function () {
    $buffer = ByteBuffer::fromBinary('Hello World 😄');

    expect($buffer->toHex())->toBe($this->expected);
});

it('should initialise from hex', function () {
    $buffer = ByteBuffer::fromHex('48656c6c6f20576f726c6420f09f9884');

    expect($buffer->toHex())->toBe($this->expected);
});

it('should fail to initialise from hex', function () {
    expect(fn () => ByteBuffer::fromHex('😄'))->toThrow(\InvalidArgumentException::class);
});

it('should initialise from utf8', function () {
    $buffer = ByteBuffer::fromUTF8('Hello World 😄');

    expect($buffer->toHex())->toBe($this->expected);
});

it('should initialise from base64', function () {
    $buffer = ByteBuffer::fromBase64(base64_encode('Hello World 😄'));

    expect($buffer->toHex())->toBe($this->expected);
});

it('should initialise from array', function () {
    $buffer = ByteBuffer::fromArray(str_split('Hello World 😄'));

    expect($buffer->toHex())->toBe($this->expected);
});

it('should initialise from string as binary', function () {
    $buffer = ByteBuffer::fromString('Hello World 😄', 'binary');

    expect($buffer->toHex())->toBe($this->expected);
});

it('should initialise from string as hex', function () {
    $buffer = ByteBuffer::fromString('48656c6c6f20576f726c6420f09f9884', 'hex');

    expect($buffer->toHex())->toBe($this->expected);
});

it('should initialise from string as utf8', function () {
    $buffer = ByteBuffer::fromString('Hello World 😄', 'utf8');

    expect($buffer->toHex())->toBe($this->expected);
});

it('should initialise from string as base64', function () {
    $buffer = ByteBuffer::fromString(base64_encode('Hello World 😄'), 'base64');

    expect($buffer->toHex())->toBe($this->expected);
});

it('should throw for invalid type', function () {
    expect(fn () => ByteBuffer::fromString('Hello World 😄', '_INVALID_'))
        ->toThrow(\InvalidArgumentException::class);
});
