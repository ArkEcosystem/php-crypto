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

namespace ArkEcosystem\Tests\Crypto\Unit\Utils;

use ArkEcosystem\Crypto\Utils\Base58;
use ArkEcosystem\Tests\Crypto\TestCase;
use InvalidArgumentException;

/**
 * @covers \ArkEcosystem\Crypto\Utils\Base58
 */
class Base58Test extends TestCase
{
    /** @test */
    public function it_should_encode_and_decode_binary()
    {
        $this->assertSame('StV1DL6CwTryKyV', Base58::encode('hello world'));
        $this->assertSame('hello world', Base58::decode('StV1DL6CwTryKyV'));
    }

    /** @test */
    public function it_should_keep_leading_zero_bytes()
    {
        $this->assertSame('11', Base58::encode("\0\0"));
        $this->assertSame("\0\0", Base58::decode('11'));
        $this->assertSame('112', Base58::encode("\0\0\x01"));
        $this->assertSame("\0\0\x01", Base58::decode('112'));
    }

    /** @test */
    public function it_should_handle_empty_values()
    {
        $this->assertSame('', Base58::encode(''));
        $this->assertSame('', Base58::decode(''));
    }

    /** @test */
    public function it_should_reject_invalid_characters()
    {
        $this->expectException(InvalidArgumentException::class);

        Base58::decode('0OIl');
    }

    /** @test */
    public function it_should_round_trip_with_a_checksum()
    {
        $this->assertSame("\x1e".str_repeat("\xab", 20), Base58::decodeCheck(Base58::encodeCheck("\x1e".str_repeat("\xab", 20))));
    }

    /** @test */
    public function it_should_decode_an_address_with_a_checksum()
    {
        $this->assertSame(
            '1e'.bin2hex(hash('ripemd160', hex2bin('034151a3ec46b5670a682b0a63394f863587d1bc97483b1b6c70eb58e7f0aed192'), true)),
            bin2hex(Base58::decodeCheck('D61mfSggzbvQgTUe6JhYKH2doHaqJ3Dyib'))
        );
    }

    /** @test */
    public function it_should_reject_an_invalid_checksum()
    {
        $this->expectException(InvalidArgumentException::class);

        Base58::decodeCheck('D61mfSggzbvQgTUe6JhYKH2doHaqJ3Dyic');
    }

    /** @test */
    public function it_should_reject_values_too_short_for_a_checksum()
    {
        $this->expectException(InvalidArgumentException::class);

        Base58::decodeCheck('2');
    }
}
