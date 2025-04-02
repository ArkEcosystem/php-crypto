<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;

/*
 * @covers \ArkEcosystem\Crypto\ByteBuffer\Concerns\Reads\Hex
 */

test('it should read hex', function () {
    $buffer = ByteBuffer::new(0);
    $buffer->writeHex('48656c6c6f20576f726c64');
    $buffer->position(0);

    expect($buffer->internalSize())->toBe(11);
    expect($buffer->readHex(22))->toBe('48656c6c6f20576f726c64');
});

test('it should read hex as string', function () {
    $buffer = ByteBuffer::new(0);
    $buffer->writeHex('48656c6c6f20576f726c64');
    $buffer->position(0);

    expect($buffer->internalSize())->toBe(11);
    expect($buffer->readHexString(22))->toBe('Hello World');
});
