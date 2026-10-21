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

namespace ArkEcosystem\Tests\Crypto\Unit\Identities;

use ArkEcosystem\Crypto\Binary\Bytes;
use ArkEcosystem\Crypto\Identities\PrivateKey as TestClass;
use ArkEcosystem\Crypto\Networks\Devnet;
use ArkEcosystem\Crypto\Networks\Testnet;
use ArkEcosystem\Tests\Crypto\TestCase;
use InvalidArgumentException;

/**
 * This is the address test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @covers \ArkEcosystem\Crypto\Identities\PrivateKey
 */
class PrivateKeyTest extends TestCase
{
    /** @test */
    public function it_should_get_the_private_key_from_passphrase()
    {
        $fixture = $this->getFixture('identity');

        $actual = TestClass::fromPassphrase($fixture['passphrase']);

        $this->assertSame($fixture['data']['privateKey'], $actual->getHex());
    }

    /** @test */
    public function it_should_get_the_private_key_from_hex()
    {
        $fixture = $this->getFixture('identity');

        $actual = TestClass::fromHex($fixture['data']['privateKey']);

        $this->assertSame($fixture['data']['privateKey'], $actual->getHex());
    }

    /** @test */
    public function it_should_get_the_private_key_from_wif()
    {
        $fixture = $this->getFixture('identity');

        $actual = TestClass::fromWif($fixture['data']['wif']);

        $this->assertSame($fixture['data']['privateKey'], $actual->getHex());
    }

    /** @test */
    public function it_should_get_the_private_key_from_bytes()
    {
        $fixture = $this->getFixture('identity');

        $actual = TestClass::fromHex(Bytes::fromHex($fixture['data']['privateKey']));

        $this->assertSame($fixture['data']['privateKey'], $actual->getHex());
        $this->assertSame($fixture['data']['privateKey'], $actual->getBuffer()->getHex());
        $this->assertSame(hex2bin($fixture['data']['privateKey']), $actual->getBinary());
    }

    /** @test */
    public function it_should_derive_the_public_key()
    {
        $fixture = $this->getFixture('identity');

        $actual = TestClass::fromPassphrase($fixture['passphrase'])->getPublicKey();

        $this->assertSame($fixture['data']['publicKey'], $actual->getHex());
    }

    /** @test */
    public function it_should_encode_the_private_key_as_wif()
    {
        $fixture = $this->getFixture('identity');

        $privateKey = TestClass::fromPassphrase($fixture['passphrase']);

        $this->assertSame($fixture['data']['wif'], $privateKey->toWif());
        $this->assertSame($fixture['data']['wif'], $privateKey->toWif(Devnet::new()));
        $this->assertSame($privateKey->getHex(), TestClass::fromWif($privateKey->toWif(Testnet::new()), Testnet::new())->getHex());
    }

    /** @test */
    public function it_should_read_an_uncompressed_wif()
    {
        $fixture = $this->getFixture('identity');

        $uncompressed = \ArkEcosystem\Crypto\Utils\Base58::encodeCheck("\xaa".hex2bin($fixture['data']['privateKey']));

        $this->assertSame($fixture['data']['privateKey'], TestClass::fromWif($uncompressed)->getHex());
    }

    /** @test */
    public function it_should_reject_a_wif_from_another_network()
    {
        $this->expectException(InvalidArgumentException::class);

        TestClass::fromWif($this->getFixture('identity')['data']['wif'], Testnet::new());
    }

    /** @test */
    public function it_should_reject_a_wif_with_an_invalid_length()
    {
        $this->expectException(InvalidArgumentException::class);

        TestClass::fromWif(\ArkEcosystem\Crypto\Utils\Base58::encodeCheck("\xaa".str_repeat("\x01", 10)));
    }

    /** @test */
    public function it_should_reject_a_wif_with_an_invalid_compression_flag()
    {
        $this->expectException(InvalidArgumentException::class);

        TestClass::fromWif(\ArkEcosystem\Crypto\Utils\Base58::encodeCheck("\xaa".str_repeat("\x01", 32)."\x02"));
    }

    /** @test */
    public function it_should_reject_a_malformed_private_key()
    {
        $this->expectException(InvalidArgumentException::class);

        TestClass::fromHex('not-a-private-key');
    }

    /** @test */
    public function it_should_reject_a_private_key_outside_the_curve_order()
    {
        $this->expectException(InvalidArgumentException::class);

        TestClass::fromHex(str_repeat('f', 64));
    }

    /** @test */
    public function it_should_reject_a_zero_private_key()
    {
        $this->expectException(InvalidArgumentException::class);

        TestClass::fromHex(str_repeat('0', 64));
    }

    /** @test */
    public function it_should_sign_deterministically_with_a_low_s_value()
    {
        $privateKey = TestClass::fromPassphrase($this->passphrase);
        $hash       = hash('sha256', 'Hello World', true);

        $signature = $privateKey->sign($hash);

        $this->assertSame($signature->getHex(), $privateKey->sign(new Bytes($hash))->getHex());
        $this->assertSame($this->getFixture('message-v2')['data']['signature'], $signature->getHex());
        $this->assertTrue($privateKey->getPublicKey()->verify($hash, $signature));
    }
}
