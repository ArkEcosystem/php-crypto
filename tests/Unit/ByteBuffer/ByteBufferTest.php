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

namespace ArkEcosystem\Tests\Crypto;

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;

it('should get the value at the given offset', function () {
    $buffer = ByteBuffer::new('Hello World');

    $this->assertSame('e', $buffer->__get(1));
});

it('should set the value at the given offset', function () {
    $buffer = ByteBuffer::new('Hello World');
    $buffer->__set(1, 'X');

    $this->assertSame('X', $buffer->__get(1));
});

it('should check if the offset exists', function () {
    $buffer = ByteBuffer::new('Hello World');

    $this->assertTrue($buffer->__isset(1));
});

it('should unset the value at the given offset', function () {
    $buffer = ByteBuffer::new('Hello World');
    $buffer->__unset(1);

    $this->assertFalse($buffer->__isset(1));
});

it('should initialise from array', function () {
    $buffer = ByteBuffer::new(str_split('Hello World'));

    $this->assertInstanceOf(ByteBuffer::class, $buffer);
    $this->assertSame(11, $buffer->internalSize());
});

it('should initialise from integer', function () {
    $buffer = ByteBuffer::new(11);

    $this->assertInstanceOf(ByteBuffer::class, $buffer);
    $this->assertSame(11, $buffer->internalSize());
});

it('should initialise from string', function () {
    $buffer = ByteBuffer::new('Hello World');

    $this->assertInstanceOf(ByteBuffer::class, $buffer);
    $this->assertSame(11, $buffer->internalSize());
});

it('should throw for invalid type', function () {
    $this->expectException(\InvalidArgumentException::class);

    $buffer = ByteBuffer::new(123.456);
});

it('should allocate the given number of bytes', function () {
    $buffer = ByteBuffer::allocate(11);

    $this->assertInstanceOf(ByteBuffer::class, $buffer);
    $this->assertSame(11, $buffer->internalSize());
});

it('should fail to allocate the given number of bytes', function () {
    $this->expectException(\InvalidArgumentException::class);

    ByteBuffer::allocate(-1);
});

it('should initialise the buffer', function () {
    $buffer = ByteBuffer::allocate(11);
    $buffer->initializeBuffer(11, 'Hello World');

    $this->assertSame('Hello World', $buffer->toUTF8());
    $this->assertSame(11, $buffer->internalSize());
});

it('should pack the given value', function () {
    $buffer = ByteBuffer::allocate(11);
    $buffer->pack('C', 255, 0);

    $this->assertSame(255, unpack('C', $buffer->offsetGet(0))[1]);
});

it('should unpack the given value', function () {
    $buffer = ByteBuffer::allocate(11);
    $buffer->pack('C', 255, 0);
    $buffer->position(0);

    $this->assertSame(255, $buffer->unpack('C'));
});

it('should get the value', function () {
    $buffer = ByteBuffer::allocate(11);
    $buffer->pack('C', 255, 0);

    $this->assertSame(255, unpack('C', $buffer->get(0))[1]);
});

it('should concat the given buffers', function () {
    $hello = ByteBuffer::new('Hello');
    $world = ByteBuffer::new('World');

    $buffer = ByteBuffer::concat($hello, $world);

    $this->assertSame('HelloWorld', $buffer->toUTF8());
});

it('should append the given buffer', function () {
    $buffer = ByteBuffer::new('Hello');
    $buffer->append(ByteBuffer::new('World'));

    $this->assertSame('HelloWorld', $buffer->toUTF8());
    $this->assertSame($buffer->capacity(), $buffer->current()); // offset should be at the end of new buffer
});

it('should append the given string', function () {
    $buffer = ByteBuffer::new('Hello');
    $buffer->append('World');

    $this->assertSame('HelloWorld', $buffer->toUTF8());
    $this->assertSame($buffer->capacity(), $buffer->current()); // offset should be at the end of new buffer
});

it('should append the given buffer to another', function () {
    $buffer = ByteBuffer::new('Hello');

    ByteBuffer::new('World')->appendTo($buffer);

    $this->assertSame('HelloWorld', $buffer->toUTF8());
    $this->assertSame($buffer->capacity(), $buffer->current()); // offset should be at the end of new buffer
});

it('should prepend the given buffer', function () {
    $buffer = ByteBuffer::new('World');
    $buffer->prepend(ByteBuffer::new('Hello'));

    $this->assertSame('HelloWorld', $buffer->toUTF8());
    $this->assertSame($buffer->capacity(), $buffer->current()); // offset should be at the end of new buffer
});

it('should prepend the given string', function () {
    $buffer = ByteBuffer::new('World');
    $buffer->prepend('Hello');

    $this->assertSame('HelloWorld', $buffer->toUTF8());
    $this->assertSame($buffer->capacity(), $buffer->current()); // offset should be at the end of new buffer
});

it('should prepend the given buffer to another', function () {
    $buffer = ByteBuffer::new('World');

    ByteBuffer::new('Hello')->prependTo($buffer);

    $this->assertSame('HelloWorld', $buffer->toUTF8());
    $this->assertSame($buffer->capacity(), $buffer->current()); // offset should be at the end of new buffer
});

it('should fill the buffer with the given number of bytes', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->fill(11);

    $this->assertSame(11, $buffer->internalSize());
});

it('should fill the buffer starting from current position', function () {
    $buffer = ByteBuffer::new('hello');
    $buffer->position(4);
    $buffer->fill(11);

    $this->assertSame(4 + 11, $buffer->internalSize());
});

it('should flip the buffer contents', function () {
    $buffer = ByteBuffer::new('Hello World');
    $buffer->flip();

    $this->assertSame(11, $buffer->internalSize());
    $this->assertSame(0, $buffer->current());
    $this->assertSame('dlroW olleH', $buffer->toUTF8());
});

it('should set the byte order', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->order(0);

    $this->assertTrue($buffer->isBigEndian());
});

it('should reverse the buffer contents', function () {
    $buffer = ByteBuffer::new('Hello World');
    $buffer->reverse();

    $this->assertSame('dlroW olleH', $buffer->toUTF8());
});

it('should slice the buffer contents', function () {
    $buffer = ByteBuffer::new('Hello World');

    $this->assertSame(str_split('Hello'), $buffer->slice(0, 5));
});

it('should fail to slice the buffer contents if offset is to big', function () {
    $this->expectException(\InvalidArgumentException::class);

    ByteBuffer::new('Hello World')->slice(16, 5);
});

it('should fail to slice the buffer contents if length is to big', function () {
    $this->expectException(\InvalidArgumentException::class);

    ByteBuffer::new('Hello World')->slice(0, 16);
});

it('should compare if the buffers are equal', function () {
    $buffer1 = ByteBuffer::allocate(11);
    $buffer2 = ByteBuffer::allocate(11);

    $this->assertTrue($buffer1->equals($buffer2));
});

it('should test if the given value is a byte buffer', function () {
    $buffer = ByteBuffer::allocate(11);

    $this->assertTrue($buffer->isByteBuffer($buffer));
});

it('should test if the buffer is big endian', function () {
    $buffer = ByteBuffer::allocate(11);
    $buffer->order(0);

    $this->assertTrue($buffer->isBigEndian());
});

it('should test if the buffer is little endian', function () {
    $buffer = ByteBuffer::allocate(11);
    $buffer->order(1);

    $this->assertTrue($buffer->isLittleEndian());
});

it('should test if the buffer is machine byte', function () {
    $buffer = ByteBuffer::allocate(11);
    $buffer->order(2);

    $this->assertTrue($buffer->isMachineByte());
});
