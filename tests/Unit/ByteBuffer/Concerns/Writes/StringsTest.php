<?php

declare(strict_types=1);

/*
 * This file is part of Ark PHP Crypto.
 *
 * (c) Ark Ecosystem <info@ark.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ArkEcosystem\Tests\Crypto\Concerns\Writes;

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;

it('should write bytes', function () {
    $buffer = ByteBuffer::new(0);
    $buffer->writeBytes('Hello World');

    $this->assertSame(11, $buffer->internalSize());
});

it('should write string', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeString('Hello World');

    $this->assertSame(11, $buffer->internalSize());
});

it('should write utf8 string', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeUTF8String('Hello World 😄');

    $this->assertSame(20, $buffer->internalSize());
});

it('should write c string', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeCString('Hello World');

    $this->assertSame(12, $buffer->internalSize());
    $this->assertSame('48656c6c6f20576f726c6400', $buffer->toHex());
});

it('should write i string', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeIString('Hello World');

    $this->assertSame(15, $buffer->internalSize());
    $this->assertSame('0000000b48656c6c6f20576f726c64', $buffer->toHex());
});

it('should write v string', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeVString('Hello World');

    $this->assertSame(12, $buffer->internalSize());
    $this->assertSame('0b48656c6c6f20576f726c64', $buffer->toHex());
});
