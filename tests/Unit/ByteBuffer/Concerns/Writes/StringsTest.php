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
  * @covers \ArkEcosystem\Crypto\ByteBuffer\Concerns\Writes\Strings
  */
class StringsTest extends TestCase
{
    /** @test */
    public function it_should_write_bytes()
    {
        $buffer = ByteBuffer::new(0);
        $buffer->writeBytes('Hello World');

        $this->assertSame(11, $buffer->internalSize());
    }

    /** @test */
    public function it_should_write_string()
    {
        $buffer = ByteBuffer::new(1);
        $buffer->writeString('Hello World');

        $this->assertSame(11, $buffer->internalSize());
    }

    /** @test */
    public function it_should_write_utf8_string()
    {
        $buffer = ByteBuffer::new(1);
        $buffer->writeUTF8String('Hello World 😄');

        $this->assertSame(20, $buffer->internalSize());
    }

    /** @test */
    public function it_should_write_c_string()
    {
        $buffer = ByteBuffer::new(1);
        $buffer->writeCString('Hello World');

        $this->assertSame(12, $buffer->internalSize());
        $this->assertSame('48656c6c6f20576f726c6400', $buffer->toHex());
    }

    /** @test */
    public function it_should_write_i_string()
    {
        $buffer = ByteBuffer::new(1);
        $buffer->writeIString('Hello World');

        $this->assertSame(15, $buffer->internalSize());
        $this->assertSame('0000000b48656c6c6f20576f726c64', $buffer->toHex());
    }

    /** @test */
    public function it_should_write_v_string()
    {
        $buffer = ByteBuffer::new(1);
        $buffer->writeVString('Hello World');

        $this->assertSame(12, $buffer->internalSize());
        $this->assertSame('0b48656c6c6f20576f726c64', $buffer->toHex());
    }
}
