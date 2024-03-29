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
 * This is the string reader test class.
 * @covers \ArkEcosystem\Crypto\ByteBuffer\Concerns\Reads\Strings
 */
class StringsTest extends TestCase
{
    /** @test */
    public function it_should_read_string()
    {
        $buffer = ByteBuffer::new(1);
        $buffer->writeString('Hello World');
        $buffer->position(0);

        $this->assertSame('Hello World', $buffer->readString(11));
    }

    /** @test */
    public function it_should_read_utf8_string()
    {
        $buffer = ByteBuffer::new(1);
        $buffer->writeUTF8String('Hello World 😄');
        $buffer->position(0);

        $this->assertSame('Hello World 😄', $buffer->readUTF8String(20));
    }

    /** @test */
    public function it_should_read_c_string()
    {
        $buffer = ByteBuffer::new(1);
        $buffer->writeCString('Hello World ');
        $buffer->position(0);

        $this->assertSame('Hello World', $buffer->readCString(11));
    }

    /** @test */
    public function it_should_read_i_string()
    {
        $buffer = ByteBuffer::new(1);
        $buffer->writeIString('Hello World');
        $buffer->position(0);

        $this->assertSame('Hello World', $buffer->readIString(11));
    }

    /** @test */
    public function it_should_write_v_string()
    {
        $buffer = ByteBuffer::new(1);
        $buffer->writeVString('Hello World');
        $buffer->position(0);

        $this->assertSame('Hello World', $buffer->readVString(11));
    }
}
