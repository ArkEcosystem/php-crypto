<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Unit\Utils;

use ArkEcosystem\Crypto\Utils\Abi\ArgumentDecoder;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ArkEcosystem\Crypto\Utils\Abi\ArgumentDecoder
 */
class ArgumentDecoderTest extends TestCase
{
    /** @test */
    public function it_should_decode_address()
    {
        $payload  = '000000000000000000000000512F366D524157BcF734546eB29a6d687B762255';
        $expected = '0x512F366D524157BcF734546eB29a6d687B762255';

        $decoder = new ArgumentDecoder($payload);

        $this->assertSame($expected, $decoder->decodeAddress());
    }

    /** @test */
    public function it_should_decode_unsigned_int()
    {
        $payload  = '000000000000000000000000000000000000000000000000016345785d8a0000';
        $expected = '100000000000000000';

        $decoder = new ArgumentDecoder($payload);

        $this->assertSame($expected, $decoder->decodeUnsignedInt());
    }

    /** @test */
    public function it_should_decode_signed_int()
    {
        $payload  = '000000000000000000000000000000000000000000000000016345785d8a0000';
        $expected = '100000000000000000';

        $decoder = new ArgumentDecoder($payload);

        $this->assertSame($expected, $decoder->decodeSignedInt());
    }

    /** @test */
    public function it_should_decode_bool_as_true()
    {
        $payload  = '0000000000000000000000000000000000000000000000000000000000000001';
        $expected = true;

        $decoder = new ArgumentDecoder($payload);

        $this->assertSame($expected, $decoder->decodeBool());
    }

    /** @test */
    public function it_should_decode_bool_as_false()
    {
        $payload  = '0000000000000000000000000000000000000000000000000000000000000000';
        $expected = false;

        $decoder = new ArgumentDecoder($payload);

        $this->assertSame($expected, $decoder->decodeBool());
    }
}
