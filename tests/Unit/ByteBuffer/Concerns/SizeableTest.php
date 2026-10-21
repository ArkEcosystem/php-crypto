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

it('should return the capacity', function () {
    $buffer = ByteBuffer::new(8);

    $this->assertSame(8, $buffer->capacity());
});

it('should return the internal capacity', function () {
    $buffer = ByteBuffer::new(8);

    $this->assertSame(8, $buffer->internalSize());
});

it('should ensure the given capacity', function () {
    $buffer = ByteBuffer::new('Hello World');
    $buffer->ensureCapacity(32);

    $this->assertSame(32, $buffer->capacity());
});

it('should keep the given capacity', function () {
    $buffer = ByteBuffer::new('Hello World');

    $this->assertInstanceOf(ByteBuffer::class, $buffer->ensureCapacity(5));
    $this->assertSame(11, $buffer->capacity());
});

it('should resize the buffer', function () {
    $buffer = ByteBuffer::new('Hello World');
    $buffer->resize(32);

    $this->assertSame(32, $buffer->capacity());
});

it('should return the remaining bytes', function () {
    $buffer = ByteBuffer::new('Hello World');
    $buffer->remaining();

    $this->assertSame(11, $buffer->remaining());
});
