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

namespace ArkEcosystem\Tests\Crypto\Concerns\Reads;

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;

it('should read string', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeString('Hello World');
    $buffer->position(0);

    $this->assertSame('Hello World', $buffer->readString(11));
});

it('should read utf8 string', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeUTF8String('Hello World 😄');
    $buffer->position(0);

    $this->assertSame('Hello World 😄', $buffer->readUTF8String(20));
});

it('should read c string', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeCString('Hello World ');
    $buffer->position(0);

    $this->assertSame('Hello World', $buffer->readCString(11));
});

it('should read i string', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeIString('Hello World');
    $buffer->position(0);

    $this->assertSame('Hello World', $buffer->readIString(11));
});

it('should write v string', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeVString('Hello World');
    $buffer->position(0);

    $this->assertSame('Hello World', $buffer->readVString(11));
});
