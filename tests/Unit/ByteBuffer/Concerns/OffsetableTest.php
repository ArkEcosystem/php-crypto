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

it('should get the value at the given offset', function () {
    $buffer = ByteBuffer::new('Hello World');

    $this->assertSame('e', $buffer->offsetGet(1));
});

it('should set the value at the given offset', function () {
    $buffer = ByteBuffer::new('Hello World');
    $buffer->offsetSet(1, 'X');

    $this->assertSame('X', $buffer->offsetGet(1));
});

it('should check if the offset exists', function () {
    $buffer = ByteBuffer::new('Hello World');

    $this->assertTrue($buffer->offsetExists(1));
});

it('should unset the value at the given offset', function () {
    $buffer = ByteBuffer::new('Hello World');
    $buffer->offsetUnset(1);

    $this->assertFalse($buffer->offsetExists(1));
});
