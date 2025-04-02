<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;

/*
 * @covers \ArkEcosystem\Crypto\ByteBuffer\Concerns\Reads\Floats
 */
test('it should read float32', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeFloat32(8.0);
    $buffer->position(0);

    expect($buffer->readFloat32())->toBe(8.0);
});

test('it should read float64', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeFloat64(8.0);
    $buffer->position(0);

    expect($buffer->readFloat64())->toBe(8.0);
});

test('it should read double', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeDouble(8.0);
    $buffer->position(0);

    expect($buffer->readDouble())->toBe(8.0);
});
