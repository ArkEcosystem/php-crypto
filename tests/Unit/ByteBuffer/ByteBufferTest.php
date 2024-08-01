<?php

declare(strict_types=1);



namespace ArkEcosystem\Tests\Crypto;

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;
use PHPUnit\Framework\TestCase;

/**
  * @covers \ArkEcosystem\Crypto\ByteBuffer\ByteBuffer
  */
class ByteBufferTest extends TestCase
{
    /** @test */
    public function it_should_get_the_value_at_the_given_offset()
    {
        $buffer = ByteBuffer::new('Hello World');

        $this->assertSame('e', $buffer->__get(1));
    }

    /** @test */
    public function it_should_set_the_value_at_the_given_offset()
    {
        $buffer = ByteBuffer::new('Hello World');
        $buffer->__set(1, 'X');

        $this->assertSame('X', $buffer->__get(1));
    }

    /** @test */
    public function it_should_check_if_the_offset_exists()
    {
        $buffer = ByteBuffer::new('Hello World');

        $this->assertTrue($buffer->__isset(1));
    }

    /** @test */
    public function it_should_unset_the_value_at_the_given_offset()
    {
        $buffer = ByteBuffer::new('Hello World');
        $buffer->__unset(1);

        $this->assertFalse($buffer->__isset(1));
    }

    /** @test */
    public function it_should_initialise_from_array()
    {
        $buffer = ByteBuffer::new(str_split('Hello World'));

        $this->assertInstanceOf(ByteBuffer::class, $buffer);
        $this->assertSame(11, $buffer->internalSize());
    }

    /** @test */
    public function it_should_initialise_from_integer()
    {
        $buffer = ByteBuffer::new(11);

        $this->assertInstanceOf(ByteBuffer::class, $buffer);
        $this->assertSame(11, $buffer->internalSize());
    }

    /** @test */
    public function it_should_initialise_from_string()
    {
        $buffer = ByteBuffer::new('Hello World');

        $this->assertInstanceOf(ByteBuffer::class, $buffer);
        $this->assertSame(11, $buffer->internalSize());
    }

    /** @test */
    public function it_should_throw_for_invalid_type()
    {
        $this->expectException(\InvalidArgumentException::class);

        $buffer = ByteBuffer::new(123.456);
    }

    /** @test */
    public function it_should_allocate_the_given_number_of_bytes()
    {
        $buffer = ByteBuffer::allocate(11);

        $this->assertInstanceOf(ByteBuffer::class, $buffer);
        $this->assertSame(11, $buffer->internalSize());
    }

    /** @test */
    public function it_should_fail_to_allocate_the_given_number_of_bytes()
    {
        $this->expectException(\InvalidArgumentException::class);

        ByteBuffer::allocate(-1);
    }

    /** @test */
    public function it_should_initialise_the_buffer()
    {
        $buffer = ByteBuffer::allocate(11);
        $buffer->initializeBuffer(11, 'Hello World');

        $this->assertSame('Hello World', $buffer->toUTF8());
        $this->assertSame(11, $buffer->internalSize());
    }

    /** @test */
    public function it_should_pack_the_given_value()
    {
        $buffer = ByteBuffer::allocate(11);
        $buffer->pack('C', 255, 0);

        $this->assertSame(255, unpack('C', $buffer->offsetGet(0))[1]);
    }

    /** @test */
    public function it_should_unpack_the_given_value()
    {
        $buffer = ByteBuffer::allocate(11);
        $buffer->pack('C', 255, 0);
        $buffer->position(0);

        $this->assertSame(255, $buffer->unpack('C'));
    }

    /** @test */
    public function it_should_get_the_value()
    {
        $buffer = ByteBuffer::allocate(11);
        $buffer->pack('C', 255, 0);

        $this->assertSame(255, unpack('C', $buffer->get(0))[1]);
    }

    /** @test */
    public function it_should_concat_the_given_buffers()
    {
        $hello = ByteBuffer::new('Hello');
        $world = ByteBuffer::new('World');

        $buffer = ByteBuffer::concat($hello, $world);

        $this->assertSame('HelloWorld', $buffer->toUTF8());
    }

    /** @test */
    public function it_should_append_the_given_buffer()
    {
        $buffer = ByteBuffer::new('Hello');
        $buffer->append(ByteBuffer::new('World'));

        $this->assertSame('HelloWorld', $buffer->toUTF8());
        $this->assertSame($buffer->capacity(), $buffer->current()); // offset should be at the end of new buffer
    }

    /** @test */
    public function it_should_append_the_given_string()
    {
        $buffer = ByteBuffer::new('Hello');
        $buffer->append('World');

        $this->assertSame('HelloWorld', $buffer->toUTF8());
        $this->assertSame($buffer->capacity(), $buffer->current()); // offset should be at the end of new buffer
    }

    /** @test */
    public function it_should_append_the_given_buffer_to_another()
    {
        $buffer = ByteBuffer::new('Hello');

        ByteBuffer::new('World')->appendTo($buffer);

        $this->assertSame('HelloWorld', $buffer->toUTF8());
        $this->assertSame($buffer->capacity(), $buffer->current()); // offset should be at the end of new buffer
    }

    /** @test */
    public function it_should_prepend_the_given_buffer()
    {
        $buffer = ByteBuffer::new('World');
        $buffer->prepend(ByteBuffer::new('Hello'));

        $this->assertSame('HelloWorld', $buffer->toUTF8());
        $this->assertSame($buffer->capacity(), $buffer->current()); // offset should be at the end of new buffer
    }

    /** @test */
    public function it_should_prepend_the_given_string()
    {
        $buffer = ByteBuffer::new('World');
        $buffer->prepend('Hello');

        $this->assertSame('HelloWorld', $buffer->toUTF8());
        $this->assertSame($buffer->capacity(), $buffer->current()); // offset should be at the end of new buffer
    }

    /** @test */
    public function it_should_prepend_the_given_buffer_to_another()
    {
        $buffer = ByteBuffer::new('World');

        ByteBuffer::new('Hello')->prependTo($buffer);

        $this->assertSame('HelloWorld', $buffer->toUTF8());
        $this->assertSame($buffer->capacity(), $buffer->current()); // offset should be at the end of new buffer
    }

    /** @test */
    public function it_should_fill_the_buffer_with_the_given_number_of_bytes()
    {
        $buffer = ByteBuffer::new(1);
        $buffer->fill(11);

        $this->assertSame(11, $buffer->internalSize());
    }

    /** @test */
    public function it_should_fill_the_buffer_starting_from_current_position()
    {
        $buffer = ByteBuffer::new('hello');
        $buffer->position(4);
        $buffer->fill(11);

        $this->assertSame(4 + 11, $buffer->internalSize());
    }

    /** @test */
    public function it_should_flip_the_buffer_contents()
    {
        $buffer = ByteBuffer::new('Hello World');
        $buffer->flip();

        $this->assertSame(11, $buffer->internalSize());
        $this->assertSame(0, $buffer->current());
        $this->assertSame('dlroW olleH', $buffer->toUTF8());
    }

    /** @test */
    public function it_should_set_the_byte_order()
    {
        $buffer = ByteBuffer::new(1);
        $buffer->order(0);

        $this->assertTrue($buffer->isBigEndian());
    }

    /** @test */
    public function it_should_reverse_the_buffer_contents()
    {
        $buffer = ByteBuffer::new('Hello World');
        $buffer->reverse();

        $this->assertSame('dlroW olleH', $buffer->toUTF8());
    }

    /** @test */
    public function it_should_slice_the_buffer_contents()
    {
        $buffer = ByteBuffer::new('Hello World');

        $this->assertSame(str_split('Hello'), $buffer->slice(0, 5));
    }

    /** @test */
    public function it_should_fail_to_slice_the_buffer_contents_if_offset_is_to_big()
    {
        $this->expectException(\InvalidArgumentException::class);

        ByteBuffer::new('Hello World')->slice(16, 5);
    }

    /** @test */
    public function it_should_fail_to_slice_the_buffer_contents_if_length_is_to_big()
    {
        $this->expectException(\InvalidArgumentException::class);

        ByteBuffer::new('Hello World')->slice(0, 16);
    }

    /** @test */
    public function it_should_compare_if_the_buffers_are_equal()
    {
        $buffer1 = ByteBuffer::allocate(11);
        $buffer2 = ByteBuffer::allocate(11);

        $this->assertTrue($buffer1->equals($buffer2));
    }

    /** @test */
    public function it_should_test_if_the_given_value_is_a_byte_buffer()
    {
        $buffer = ByteBuffer::allocate(11);

        $this->assertTrue($buffer->isByteBuffer($buffer));
    }

    /** @test */
    public function it_should_test_if_the_buffer_is_big_endian()
    {
        $buffer = ByteBuffer::allocate(11);
        $buffer->order(0);

        $this->assertTrue($buffer->isBigEndian());
    }

    /** @test */
    public function it_should_test_if_the_buffer_is_little_endian()
    {
        $buffer = ByteBuffer::allocate(11);
        $buffer->order(1);

        $this->assertTrue($buffer->isLittleEndian());
    }

    /** @test */
    public function it_should_test_if_the_buffer_is_machine_byte()
    {
        $buffer = ByteBuffer::allocate(11);
        $buffer->order(2);

        $this->assertTrue($buffer->isMachineByte());
    }
}
