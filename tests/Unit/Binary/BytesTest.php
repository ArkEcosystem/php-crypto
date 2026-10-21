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

namespace ArkEcosystem\Tests\Crypto\Unit\Binary;

use ArkEcosystem\Crypto\Binary\Bytes;
use ArkEcosystem\Tests\Crypto\TestCase;
use InvalidArgumentException;

/**
 * @covers \ArkEcosystem\Crypto\Binary\Bytes
 */
class BytesTest extends TestCase
{
    /** @test */
    public function it_should_expose_the_bytes_as_binary_hex_and_size()
    {
        $bytes = new Bytes("\x00\xff");

        $this->assertSame("\x00\xff", $bytes->getBinary());
        $this->assertSame('00ff', $bytes->getHex());
        $this->assertSame(2, $bytes->getSize());
    }

    /** @test */
    public function it_should_create_bytes_from_hex()
    {
        $this->assertSame("\xab\xcd", Bytes::fromHex('ABCD')->getBinary());
        $this->assertSame('', Bytes::fromHex('')->getBinary());
    }

    /** @test */
    public function it_should_reject_invalid_hex()
    {
        $this->expectException(InvalidArgumentException::class);

        Bytes::fromHex('abc');
    }

    /** @test */
    public function it_should_reject_non_hex_characters()
    {
        $this->expectException(InvalidArgumentException::class);

        Bytes::fromHex('zz');
    }
}
