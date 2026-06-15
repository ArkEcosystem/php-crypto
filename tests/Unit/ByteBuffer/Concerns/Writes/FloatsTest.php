<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;

/*
 * @covers \ArkEcosystem\Crypto\ByteBuffer\Concerns\Writes\Floats
 */

it('should write float32', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeFloat32(8.0);

    expect($buffer->internalSize())->toBe(4);
});

it('should write float64', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeFloat64(8.0);

    expect($buffer->internalSize())->toBe(8);
});

it('should write double', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeDouble(8.0);

    expect($buffer->internalSize())->toBe(8);
});
