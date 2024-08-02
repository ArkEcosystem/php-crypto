<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Concerns\Reads;

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ArkEcosystem\Crypto\ByteBuffer\Concerns\Reads\Floats
 */
class FloatsTest extends TestCase
{
    /** @test */
    public function it_should_read_float32()
    {
        $buffer = ByteBuffer::new(1);
        $buffer->writeFloat32(8.0);
        $buffer->position(0);

        $this->assertSame(8.0, $buffer->readFloat32());
    }

    /** @test */
    public function it_should_read_float64()
    {
        $buffer = ByteBuffer::new(1);
        $buffer->writeFloat64(8.0);
        $buffer->position(0);

        $this->assertSame(8.0, $buffer->readFloat64());
    }

    /** @test */
    public function it_should_read_double()
    {
        $buffer = ByteBuffer::new(1);
        $buffer->writeDouble(8.0);
        $buffer->position(0);

        $this->assertSame(8.0, $buffer->readDouble());
    }
}
