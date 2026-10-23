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

it('should transform to binary', function () {
    $buffer = ByteBuffer::new('Hello World 😄');

    $this->assertSame('Hello World 😄', $buffer->toBinary());
});

it('should transform to hex', function () {
    $buffer = ByteBuffer::new('Hello World 😄');

    $this->assertSame('48656c6c6f20576f726c6420f09f9884', $buffer->toHex());
});

it('should transform to utf8', function () {
    $buffer = ByteBuffer::new('Hello World 😄');

    $this->assertSame('Hello World 😄', $buffer->toUTF8());
});

it('should transform to base64', function () {
    $buffer = ByteBuffer::new('Hello World 😄');

    $this->assertSame('SGVsbG8gV29ybGQg8J+YhA==', $buffer->toBase64());
});

it('should transform to array', function () {
    $buffer = ByteBuffer::new('Hello World 😄');

    $this->assertSame(str_split('Hello World 😄'), $buffer->toArray());
});

it('should transform to gmp', function () {
    $buffer = ByteBuffer::new('Hello World 😄');

    $this->assertInstanceOf(\GMP::class, $buffer->toGmp());
});

it('should transform to gmp integer', function () {
    $buffer = ByteBuffer::new('Hello World 😄');

    $this->assertSame(8245075110447257732, $buffer->toGmpInt());
});

it('should transform to gmp string', function () {
    $buffer = ByteBuffer::new('Hello World 😄');

    $this->assertSame('96231036770496640978624582588703938692', $buffer->toGmpString());
});

it('should transform to string as binary', function () {
    $buffer = ByteBuffer::new('Hello World 😄');

    $this->assertSame('Hello World 😄', $buffer->toString('binary'));
});

it('should transform to string as hex', function () {
    $buffer = ByteBuffer::new('Hello World 😄');

    $this->assertSame('48656c6c6f20576f726c6420f09f9884', $buffer->toString('hex'));
});

it('should transform to string as utf8', function () {
    $buffer = ByteBuffer::new('Hello World 😄');

    $this->assertSame('Hello World 😄', $buffer->toString('utf8'));
});

it('should transform to string as base64', function () {
    $buffer = ByteBuffer::new('Hello World 😄');

    $this->assertSame('SGVsbG8gV29ybGQg8J+YhA==', $buffer->toString('base64'));
});

it('should throw for invalid type', function () {
    $this->expectException(\InvalidArgumentException::class);

    ByteBuffer::new('Hello World 😄')->toString('_INVALID_');
});
