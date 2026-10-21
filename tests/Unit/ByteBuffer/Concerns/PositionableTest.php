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

it('should current the offset', function () {
    $buffer = ByteBuffer::new(8);
    $buffer->current();

    $this->assertSame(0, $buffer->current());
});

it('should set the offset to the given value', function () {
    $buffer = ByteBuffer::new(8);
    $buffer->position(5);

    $this->assertSame(5, $buffer->current());
});

it('should skip the given number of bytes', function () {
    $buffer = ByteBuffer::new(8);
    $buffer->skip(2);
    $buffer->skip(3);
    $buffer->skip(1);

    $this->assertSame(6, $buffer->current());
});

it('should rewind the given number of bytes', function () {
    $buffer = ByteBuffer::new(8);
    $buffer->position(5);
    $buffer->rewind(3);
    $buffer->rewind(1);

    $this->assertSame(1, $buffer->current());
});

it('should reset the offset', function () {
    $buffer = ByteBuffer::new(8);
    $buffer->position(5);
    $this->assertSame(5, $buffer->current());

    $buffer->reset();
    $this->assertSame(0, $buffer->current());
});

it('should clear the offset', function () {
    $buffer = ByteBuffer::new(8);
    $buffer->position(5);
    $this->assertSame(5, $buffer->current());
    $this->assertSame(8, $buffer->capacity());

    $buffer->clear();
    $this->assertSame(0, $buffer->current());
    $this->assertSame(8, $buffer->capacity());
});
