<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto;

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;
use InvalidArgumentException;

/*
 * @covers \ArkEcosystem\Crypto\ByteBuffer\ByteBuffer
 */

it('should get the value at the given offset', function () {
    $buffer = ByteBuffer::new('Hello World');

    expect($buffer->__get(1))->toBe('e');
});

it('should set the value at the given offset', function () {
    $buffer = ByteBuffer::new('Hello World');
    $buffer->__set(1, 'X');

    expect($buffer->__get(1))->toBe('X');
});

it('should check if the offset exists', function () {
    $buffer = ByteBuffer::new('Hello World');

    expect($buffer->__isset(1))->toBeTrue();
});

it('should unset the value at the given offset', function () {
    $buffer = ByteBuffer::new('Hello World');
    $buffer->__unset(1);

    expect($buffer->__isset(1))->toBeFalse();
});

it('should initialise from array', function () {
    $buffer = ByteBuffer::new(str_split('Hello World'));

    expect($buffer)->toBeInstanceOf(ByteBuffer::class);
    expect($buffer->internalSize())->toBe(11);
});

it('should initialise from integer', function () {
    $buffer = ByteBuffer::new(11);

    expect($buffer)->toBeInstanceOf(ByteBuffer::class);
    expect($buffer->internalSize())->toBe(11);
});

it('should initialise from string', function () {
    $buffer = ByteBuffer::new('Hello World');

    expect($buffer)->toBeInstanceOf(ByteBuffer::class);
    expect($buffer->internalSize())->toBe(11);
});

it('should throw for invalid type', function () {
    expect(fn () => ByteBuffer::new(123.456))->toThrow(InvalidArgumentException::class);
});

it('should allocate the given number of bytes', function () {
    $buffer = ByteBuffer::allocate(11);

    expect($buffer)->toBeInstanceOf(ByteBuffer::class);
    expect($buffer->internalSize())->toBe(11);
});

it('should fail to allocate the given number of bytes', function () {
    expect(fn () => ByteBuffer::allocate(-1))->toThrow(InvalidArgumentException::class);
});

it('should initialise the buffer', function () {
    $buffer = ByteBuffer::allocate(11);
    $buffer->initializeBuffer(11, 'Hello World');

    expect($buffer->toUTF8())->toBe('Hello World');
    expect($buffer->internalSize())->toBe(11);
});

it('should pack the given value', function () {
    $buffer = ByteBuffer::allocate(11);
    $buffer->pack('C', 255, 0);

    expect(unpack('C', $buffer->offsetGet(0))[1])->toBe(255);
});

it('should unpack the given value', function () {
    $buffer = ByteBuffer::allocate(11);
    $buffer->pack('C', 255, 0);
    $buffer->position(0);

    expect($buffer->unpack('C'))->toBe(255);
});

it('should get the value', function () {
    $buffer = ByteBuffer::allocate(11);
    $buffer->pack('C', 255, 0);

    expect(unpack('C', $buffer->get(0))[1])->toBe(255);
});

it('should concat the given buffers', function () {
    $hello = ByteBuffer::new('Hello');
    $world = ByteBuffer::new('World');

    $buffer = ByteBuffer::concat($hello, $world);

    expect($buffer->toUTF8())->toBe('HelloWorld');
});

it('should append the given buffer', function () {
    $buffer = ByteBuffer::new('Hello');
    $buffer->append(ByteBuffer::new('World'));

    expect($buffer->toUTF8())->toBe('HelloWorld');
    expect($buffer->capacity())->toBe($buffer->current()); // offset should be at the end of new buffer
});

it('should append the given string', function () {
    $buffer = ByteBuffer::new('Hello');
    $buffer->append('World');

    expect($buffer->toUTF8())->toBe('HelloWorld');
    expect($buffer->capacity())->toBe($buffer->current()); // offset should be at the end of new buffer
});

it('should append the given buffer to another', function () {
    $buffer = ByteBuffer::new('Hello');

    ByteBuffer::new('World')->appendTo($buffer);

    expect($buffer->toUTF8())->toBe('HelloWorld');
    expect($buffer->capacity())->toBe($buffer->current()); // offset should be at the end of new buffer
});

it('should prepend the given buffer', function () {
    $buffer = ByteBuffer::new('World');
    $buffer->prepend(ByteBuffer::new('Hello'));

    expect($buffer->toUTF8())->toBe('HelloWorld');
    expect($buffer->capacity())->toBe($buffer->current()); // offset should be at the end of new buffer
});

it('should prepend the given string', function () {
    $buffer = ByteBuffer::new('World');
    $buffer->prepend('Hello');

    expect($buffer->toUTF8())->toBe('HelloWorld');
    expect($buffer->capacity())->toBe($buffer->current()); // offset should be at the end of new buffer
});

it('should prepend the given buffer to another', function () {
    $buffer = ByteBuffer::new('World');

    ByteBuffer::new('Hello')->prependTo($buffer);

    expect($buffer->toUTF8())->toBe('HelloWorld');
    expect($buffer->capacity())->toBe($buffer->current()); // offset should be at the end of new buffer
});

it('should fill the buffer with the given number of bytes', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->fill(11);

    expect($buffer->internalSize())->toBe(11);
});

it('should fill the buffer starting from current position', function () {
    $buffer = ByteBuffer::new('hello');
    $buffer->position(4);
    $buffer->fill(11);

    expect($buffer->internalSize())->toBe(4 + 11);
});

it('should fill the buffer starting from a different start point', function () {
    $buffer = ByteBuffer::new('hello');
    $buffer->fill(11, 4);

    expect($buffer->internalSize())->toBe(4 + 11);
});

it('should flip the buffer contents', function () {
    $buffer = ByteBuffer::new('Hello World');
    $buffer->flip();

    expect($buffer->internalSize())->toBe(11);
    expect($buffer->current())->toBe(0);
    expect($buffer->toUTF8())->toBe('dlroW olleH');
});

it('should set the byte order', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->order(0);

    expect($buffer->isBigEndian())->toBeTrue();
});

it('should reverse the buffer contents', function () {
    $buffer = ByteBuffer::new('Hello World');
    $buffer->reverse();

    expect($buffer->toUTF8())->toBe('dlroW olleH');
});

it('should slice the buffer contents', function () {
    $buffer = ByteBuffer::new('Hello World');

    expect($buffer->slice(0, 5))->toBe(str_split('Hello'));
});

it('should fail to slice the buffer contents if offset is too big', function () {
    expect(fn () => ByteBuffer::new('Hello World')->slice(16, 5))->toThrow(InvalidArgumentException::class);
});

it('should fail to slice the buffer contents if length is too big', function () {
    expect(fn () => ByteBuffer::new('Hello World')->slice(0, 16))->toThrow(InvalidArgumentException::class);
});

it('should compare if the buffers are equal', function () {
    $buffer1 = ByteBuffer::allocate(11);
    $buffer2 = ByteBuffer::allocate(11);

    expect($buffer1->equals($buffer2))->toBeTrue();
});

it('should test if the given value is a byte buffer', function () {
    $buffer = ByteBuffer::allocate(11);

    expect($buffer->isByteBuffer($buffer))->toBeTrue();
});

it('should test if the buffer is big endian', function () {
    $buffer = ByteBuffer::allocate(11);
    $buffer->order(0);

    expect($buffer->isBigEndian())->toBeTrue();
});

it('should test if the buffer is little endian', function () {
    $buffer = ByteBuffer::allocate(11);
    $buffer->order(1);

    expect($buffer->isLittleEndian())->toBeTrue();
});

it('should test if the buffer is machine byte', function () {
    $buffer = ByteBuffer::allocate(11);
    $buffer->order(2);

    expect($buffer->isMachineByte())->toBeTrue();
});
