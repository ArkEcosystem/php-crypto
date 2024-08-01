<?php

declare(strict_types=1);



namespace ArkEcosystem\Tests\Crypto;

use ArkEcosystem\Crypto\ByteBuffer\LengthMap;
use PHPUnit\Framework\TestCase;

/**
  * @covers \ArkEcosystem\Crypto\ByteBuffer\LengthMap
  */
class LengthMapTest extends TestCase
{
    /** @test */
    public function it_should_get_the_length_for_string()
    {
        $this->assertSame(33, LengthMap::get('a33'));
    }

    /** @test */
    public function it_should_get_the_length_for_float()
    {
        $this->assertSame(33, LengthMap::get('f33'));
    }

    /** @test */
    public function it_should_get_the_length_for_double()
    {
        $this->assertSame(33, LengthMap::get('d33'));
    }

    /** @test */
    public function it_should_get_the_length_for_hex_with_low_nibble()
    {
        $this->assertSame(33, LengthMap::get('h66'));
    }

    /** @test */
    public function it_should_get_the_length_for_hex_with_high_nibble()
    {
        $this->assertSame(33, LengthMap::get('H66'));
    }

    /** @test */
    public function it_should_get_the_length_from_the_array()
    {
        $this->assertSame(1, LengthMap::get('C'));
    }

    /** @test */
    public function it_should_throw_for_invalid_type()
    {
        $this->expectException(\InvalidArgumentException::class);

        LengthMap::get('_INVALID_');
    }
}
