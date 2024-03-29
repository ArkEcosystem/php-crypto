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
 * This is the positionable test class.
 * @covers \ArkEcosystem\Crypto\ByteBuffer\Concerns\Positionable
 */
class PositionableTest extends TestCase
{
    /** @test */
    public function it_should_current_the_offset()
    {
        $buffer = ByteBuffer::new(8);
        $buffer->current();

        $this->assertSame(0, $buffer->current());
    }

    /** @test */
    public function it_should_set_the_offset_to_the_given_value()
    {
        $buffer = ByteBuffer::new(8);
        $buffer->position(5);

        $this->assertSame(5, $buffer->current());
    }

    /** @test */
    public function it_should_skip_the_given_number_of_bytes()
    {
        $buffer = ByteBuffer::new(8);
        $buffer->skip(2);
        $buffer->skip(3);
        $buffer->skip(1);

        $this->assertSame(6, $buffer->current());
    }

    /** @test */
    public function it_should_rewind_the_given_number_of_bytes()
    {
        $buffer = ByteBuffer::new(8);
        $buffer->position(5);
        $buffer->rewind(3);
        $buffer->rewind(1);

        $this->assertSame(1, $buffer->current());
    }

    /** @test */
    public function it_should_reset_the_offset()
    {
        $buffer = ByteBuffer::new(8);
        $buffer->position(5);
        $this->assertSame(5, $buffer->current());

        $buffer->reset();
        $this->assertSame(0, $buffer->current());
    }

    /** @test */
    public function it_should_clear_the_offset()
    {
        $buffer = ByteBuffer::new(8);
        $buffer->position(5);
        $this->assertSame(5, $buffer->current());
        $this->assertSame(8, $buffer->capacity());

        $buffer->clear();
        $this->assertSame(0, $buffer->current());
        $this->assertSame(8, $buffer->capacity());
    }
}
