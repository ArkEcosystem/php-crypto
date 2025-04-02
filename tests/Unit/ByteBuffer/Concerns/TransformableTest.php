<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;

/**
 * @covers \ArkEcosystem\Crypto\ByteBuffer\Concerns\Transformable
 */

it('should transform to binary', function () {
    $buffer = ByteBuffer::new('Hello World 😄');

    expect($buffer->toBinary())->toBe('Hello World 😄');
});

it('should transform to hex', function () {
    $buffer = ByteBuffer::new('Hello World 😄');

    expect($buffer->toHex())->toBe('48656c6c6f20576f726c6420f09f9884');
});

it('should transform to utf8', function () {
    $buffer = ByteBuffer::new('Hello World 😄');

    expect($buffer->toUTF8())->toBe('Hello World 😄');
});

it('should transform to base64', function () {
    $buffer = ByteBuffer::new('Hello World 😄');

    expect($buffer->toBase64())->toBe('SGVsbG8gV29ybGQg8J+YhA==');
});

it('should transform to array', function () {
    $buffer = ByteBuffer::new('Hello World 😄');

    expect($buffer->toArray())->toBe(str_split('Hello World 😄'));
});

it('should transform to gmp', function () {
    $buffer = ByteBuffer::new('Hello World 😄');

    expect($buffer->toGmp())->toBeInstanceOf(\GMP::class);
});

it('should transform to gmp integer', function () {
    $buffer = ByteBuffer::new('Hello World 😄');

    expect($buffer->toGmpInt())->toBe(8245075110447257732);
});

it('should transform to gmp string', function () {
    $buffer = ByteBuffer::new('Hello World 😄');

    expect($buffer->toGmpString())->toBe('96231036770496640978624582588703938692');
});

it('should transform to string as binary', function () {
    $buffer = ByteBuffer::new('Hello World 😄');

    expect($buffer->toString('binary'))->toBe('Hello World 😄');
});

it('should transform to string as hex', function () {
    $buffer = ByteBuffer::new('Hello World 😄');

    expect($buffer->toString('hex'))->toBe('48656c6c6f20576f726c6420f09f9884');
});

it('should transform to string as utf8', function () {
    $buffer = ByteBuffer::new('Hello World 😄');

    expect($buffer->toString('utf8'))->toBe('Hello World 😄');
});

it('should transform to string as base64', function () {
    $buffer = ByteBuffer::new('Hello World 😄');

    expect($buffer->toString('base64'))->toBe('SGVsbG8gV29ybGQg8J+YhA==');
});

it('should throw for invalid type', function () {
    expect(fn () => ByteBuffer::new('Hello World 😄')->toString('_INVALID_'))
        ->toThrow(\InvalidArgumentException::class);
});
