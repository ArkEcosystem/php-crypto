<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;

/*
 * @covers \ArkEcosystem\Crypto\ByteBuffer\Concerns\Positionable
 */

it('should current the offset', function () {
    $buffer = ByteBuffer::new(8);
    $buffer->current();

    expect($buffer->current())->toBe(0);
});

it('should set the offset to the given value', function () {
    $buffer = ByteBuffer::new(8);
    $buffer->position(5);

    expect($buffer->current())->toBe(5);
});

it('should skip the given number of bytes', function () {
    $buffer = ByteBuffer::new(8);
    $buffer->skip(2);
    $buffer->skip(3);
    $buffer->skip(1);

    expect($buffer->current())->toBe(6);
});

it('should rewind the given number of bytes', function () {
    $buffer = ByteBuffer::new(8);
    $buffer->position(5);
    $buffer->rewind(3);
    $buffer->rewind(1);

    expect($buffer->current())->toBe(1);
});

it('should reset the offset', function () {
    $buffer = ByteBuffer::new(8);
    $buffer->position(5);

    expect($buffer->current())->toBe(5);

    $buffer->reset();

    expect($buffer->current())->toBe(0);
});

it('should clear the offset', function () {
    $buffer = ByteBuffer::new(8);
    $buffer->position(5);

    expect($buffer->current())->toBe(5);
    expect($buffer->capacity())->toBe(8);

    $buffer->clear();

    expect($buffer->current())->toBe(0);
    expect($buffer->capacity())->toBe(8);
});
