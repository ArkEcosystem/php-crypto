<?php

declare(strict_types=1);



namespace ArkEcosystem\Tests\Crypto\Concerns\Reads;

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;
use PHPUnit\Framework\TestCase;

/**
  * @covers \ArkEcosystem\Crypto\ByteBuffer\Concerns\Offsetable
  */
class OffsetableTest extends TestCase
{
    /** @test */
    public function it_should_get_the_value_at_the_given_offset()
    {
        $buffer = ByteBuffer::new('Hello World');

        $this->assertSame('e', $buffer->offsetGet(1));
    }

    /** @test */
    public function it_should_set_the_value_at_the_given_offset()
    {
        $buffer = ByteBuffer::new('Hello World');
        $buffer->offsetSet(1, 'X');

        $this->assertSame('X', $buffer->offsetGet(1));
    }

    /** @test */
    public function it_should_check_if_the_offset_exists()
    {
        $buffer = ByteBuffer::new('Hello World');

        $this->assertTrue($buffer->offsetExists(1));
    }

    /** @test */
    public function it_should_unset_the_value_at_the_given_offset()
    {
        $buffer = ByteBuffer::new('Hello World');
        $buffer->offsetUnset(1);

        $this->assertFalse($buffer->offsetExists(1));
    }
}
