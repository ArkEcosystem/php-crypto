<?php

declare(strict_types=1);



namespace ArkEcosystem\Tests\Crypto\Concerns\Writes;

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;
use PHPUnit\Framework\TestCase;

/**
  * @covers \ArkEcosystem\Crypto\ByteBuffer\Concerns\Writes\Floats
  */
class FloatsTest extends TestCase
{
    /** @test */
    public function it_should_write_float32()
    {
        $buffer = ByteBuffer::new(1);
        $buffer->writeFloat32(8.0);

        $this->assertSame(4, $buffer->internalSize());
    }

    /** @test */
    public function it_should_write_float64()
    {
        $buffer = ByteBuffer::new(1);
        $buffer->writeFloat64(8.0);

        $this->assertSame(8, $buffer->internalSize());
    }

    /** @test */
    public function it_should_write_double()
    {
        $buffer = ByteBuffer::new(1);
        $buffer->writeDouble(8.0);

        $this->assertSame(8, $buffer->internalSize());
    }
}
