<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;

/*
 * @covers \ArkEcosystem\Crypto\ByteBuffer\Concerns\Offsetable
 */

it('should get the value at the given offset', function () {
    $buffer = ByteBuffer::new('Hello World');

    expect($buffer->offsetGet(1))->toBe('e');
});

it('should set the value at the given offset', function () {
    $buffer = ByteBuffer::new('Hello World');
    $buffer->offsetSet(1, 'X');

    expect($buffer->offsetGet(1))->toBe('X');
});

it('should check if the offset exists', function () {
    $buffer = ByteBuffer::new('Hello World');

    expect($buffer->offsetExists(1))->toBeTrue();
});

it('should unset the value at the given offset', function () {
    $buffer = ByteBuffer::new('Hello World');
    $buffer->offsetUnset(1);

    expect($buffer->offsetExists(1))->toBeFalse();
});
