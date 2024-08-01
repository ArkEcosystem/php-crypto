<?php

declare(strict_types=1);



namespace ArkEcosystem\Tests\Crypto\Concerns\Writes;

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;
use PHPUnit\Framework\TestCase;

/**
  * @covers \ArkEcosystem\Crypto\ByteBuffer\Concerns\Writes\Hex
  */
class HexTest extends TestCase
{
    /** @test */
    public function it_should_write_hex()
    {
        $buffer = ByteBuffer::new(0);
        $buffer->writeHex('48656c6c6f20576f726c64');
        $buffer->position(0);

        $this->assertSame(11, $buffer->internalSize());
        $this->assertSame('Hello World', $buffer->readHexString(22));
    }
}
