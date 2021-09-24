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

namespace ArkEcosystem\Tests\Crypto\Concerns\Writes;

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;
use PHPUnit\Framework\TestCase;

/**
 * This is the unsigned integer writer test class.
 * @covers \ArkEcosystem\Crypto\ByteBuffer\Concerns\Writes\UnsignedInteger
 */
class UnsignedIntegerTest extends TestCase
{
    /** @test */
    public function it_should_write_uint8()
    {
        $buffer = ByteBuffer::new(1);
        $buffer->writeUInt8(8);

        $this->assertSame(1, $buffer->internalSize());
    }

    /** @test */
    public function it_should_write_uint16()
    {
        $buffer = ByteBuffer::new(1);
        $buffer->writeUInt16(16);

        $this->assertSame(2, $buffer->internalSize());
    }

    /** @test */
    public function it_should_write_uint32()
    {
        $buffer = ByteBuffer::new(1);
        $buffer->writeUInt32(32);

        $this->assertSame(4, $buffer->internalSize());
    }

    /** @test */
    public function it_should_write_uint64()
    {
        $buffer = ByteBuffer::new(1);
        $buffer->writeUInt64(64);

        $this->assertSame(8, $buffer->internalSize());
    }

    /** @test */
    public function it_should_write_ubyte()
    {
        $buffer = ByteBuffer::new(1);
        $buffer->writeUByte(8);

        $this->assertSame(1, $buffer->internalSize());
    }

    /** @test */
    public function it_should_write_ushort()
    {
        $buffer = ByteBuffer::new(1);
        $buffer->writeUShort(15);

        $this->assertSame(2, $buffer->internalSize());
    }

    /** @test */
    public function it_should_write_uint()
    {
        $buffer = ByteBuffer::new(1);
        $buffer->writeUInt(32);

        $this->assertSame(4, $buffer->internalSize());
    }

    /** @test */
    public function it_should_write_ulong()
    {
        $buffer = ByteBuffer::new(1);
        $buffer->writeULong(64);

        $this->assertSame(8, $buffer->internalSize());
    }
}
