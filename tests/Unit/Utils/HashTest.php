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

use ArkEcosystem\Crypto\Binary\Bytes;
use ArkEcosystem\Crypto\Utils\Hash;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * @covers \ArkEcosystem\Crypto\Utils\Hash
 */
class HashTest extends TestCase
{
    /** @test */
    public function it_should_hash_with_sha256()
    {
        $this->assertSame('b94d27b9934d3e08a52e52d7da7dabfac484efe37a5380ee9088f7ace2efcde9', Hash::sha256('hello world')->getHex());
        $this->assertSame(Hash::sha256('hello world')->getHex(), Hash::sha256(new Bytes('hello world'))->getHex());
    }

    /** @test */
    public function it_should_hash_with_double_sha256()
    {
        $this->assertSame('bc62d4b80d9e36da29c16c5d4d9f11731f36052c72401a76c23c0fb5a9b74423', Hash::sha256d('hello world')->getHex());
    }

    /** @test */
    public function it_should_hash_with_ripemd160()
    {
        $this->assertSame('98c615784ccb5fe5936fbc0cbe9dfdb408d92f0f', Hash::ripemd160('hello world')->getHex());
    }
}
