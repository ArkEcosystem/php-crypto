<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;

/**
 * @covers \ArkEcosystem\Crypto\ByteBuffer\Concerns\Sizeable
 */

it('should return the capacity', function () {
    $buffer = ByteBuffer::new(8);

    expect($buffer->capacity())->toBe(8);
});

it('should return the internal capacity', function () {
    $buffer = ByteBuffer::new(8);

    expect($buffer->internalSize())->toBe(8);
});

it('should ensure the given capacity', function () {
    $buffer = ByteBuffer::new('Hello World');
    $buffer->ensureCapacity(32);

    expect($buffer->capacity())->toBe(32);
});

it('should keep the given capacity', function () {
    $buffer = ByteBuffer::new('Hello World');

    expect($buffer->ensureCapacity(5))->toBeInstanceOf(ByteBuffer::class);
    expect($buffer->capacity())->toBe(11);
});

it('should resize the buffer', function () {
    $buffer = ByteBuffer::new('Hello World');
    $buffer->resize(32);

    expect($buffer->capacity())->toBe(32);
});

it('should return the remaining bytes', function () {
    $buffer = ByteBuffer::new('Hello World');
    $buffer->remaining();

    expect($buffer->remaining())->toBe(11);
});
