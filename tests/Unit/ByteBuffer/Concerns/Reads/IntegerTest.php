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
use PHPUnit\Framework\TestCase;

/**
 * This is the integer reader test class.
 * @covers \ArkEcosystem\Crypto\ByteBuffer\Concerns\Reads\Integer
 */
class IntegerTest extends TestCase
{
    /** @test */
    public function it_should_read_int8()
    {
        $buffer = ByteBuffer::new(1);
        $buffer->writeInt8(8);
        $buffer->position(0);

        $this->assertSame(8, $buffer->readInt8());
    }

    /** @test */
    public function it_should_read_int16()
    {
        $buffer = ByteBuffer::new(1);
        $buffer->writeInt16(16);
        $buffer->position(0);

        $this->assertSame(16, $buffer->readInt16());
    }

    /** @test */
    public function it_should_read_int32()
    {
        $buffer = ByteBuffer::new(1);
        $buffer->writeInt32(32);
        $buffer->position(0);

        $this->assertSame(32, $buffer->readInt32());
    }

    /** @test */
    public function it_should_read_int64()
    {
        $buffer = ByteBuffer::new(1);
        $buffer->writeInt64(64);
        $buffer->position(0);

        $this->assertSame(64, $buffer->readInt64());
    }

    /** @test */
    public function it_should_read_byte()
    {
        $buffer = ByteBuffer::new(1);
        $buffer->writeByte(8);
        $buffer->position(0);

        $this->assertSame(8, $buffer->readByte());
    }

    /** @test */
    public function it_should_read_short()
    {
        $buffer = ByteBuffer::new(1);
        $buffer->writeShort(16);
        $buffer->position(0);

        $this->assertSame(16, $buffer->readShort());
    }

    /** @test */
    public function it_should_read_int()
    {
        $buffer = ByteBuffer::new(1);
        $buffer->writeInt(32);
        $buffer->position(0);

        $this->assertSame(32, $buffer->readInt());
    }

    /** @test */
    public function it_should_read_long()
    {
        $buffer = ByteBuffer::new(1);
        $buffer->writeLong(64);
        $buffer->position(0);

        $this->assertSame(64, $buffer->readLong());
    }
}
