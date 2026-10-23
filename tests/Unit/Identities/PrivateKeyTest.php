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
use InvalidArgumentException;

it('should get the private key from passphrase', function () {
    $fixture = $this->getFixture('identity');

    $actual = TestClass::fromPassphrase($fixture['passphrase']);

    $this->assertSame($fixture['data']['privateKey'], $actual->getHex());
});

it('should get the private key from hex', function () {
    $fixture = $this->getFixture('identity');

    $actual = TestClass::fromHex($fixture['data']['privateKey']);

    $this->assertSame($fixture['data']['privateKey'], $actual->getHex());
});

it('should get the private key from wif', function () {
    $fixture = $this->getFixture('identity');

    $actual = TestClass::fromWif($fixture['data']['wif']);

    $this->assertSame($fixture['data']['privateKey'], $actual->getHex());
});

it('should get the private key from bytes', function () {
    $fixture = $this->getFixture('identity');

    $actual = TestClass::fromHex(Bytes::fromHex($fixture['data']['privateKey']));

    $this->assertSame($fixture['data']['privateKey'], $actual->getHex());
    $this->assertSame($fixture['data']['privateKey'], $actual->getBuffer()->getHex());
    $this->assertSame(hex2bin($fixture['data']['privateKey']), $actual->getBinary());
});

it('should derive the public key', function () {
    $fixture = $this->getFixture('identity');

    $actual = TestClass::fromPassphrase($fixture['passphrase'])->getPublicKey();

    $this->assertSame($fixture['data']['publicKey'], $actual->getHex());
});

it('should encode the private key as wif', function () {
    $fixture = $this->getFixture('identity');

    $privateKey = TestClass::fromPassphrase($fixture['passphrase']);

    $this->assertSame($fixture['data']['wif'], $privateKey->toWif());
    $this->assertSame($fixture['data']['wif'], $privateKey->toWif(Devnet::new()));
    $this->assertSame($privateKey->getHex(), TestClass::fromWif($privateKey->toWif(Testnet::new()), Testnet::new())->getHex());
});

it('should read an uncompressed wif', function () {
    $fixture = $this->getFixture('identity');

    $uncompressed = \ArkEcosystem\Crypto\Utils\Base58::encodeCheck("\xaa".hex2bin($fixture['data']['privateKey']));

    $this->assertSame($fixture['data']['privateKey'], TestClass::fromWif($uncompressed)->getHex());
});

it('should reject a wif from another network', function () {
    $this->expectException(InvalidArgumentException::class);

    TestClass::fromWif($this->getFixture('identity')['data']['wif'], Testnet::new());
});

it('should reject a wif with an invalid length', function () {
    $this->expectException(InvalidArgumentException::class);

    TestClass::fromWif(\ArkEcosystem\Crypto\Utils\Base58::encodeCheck("\xaa".str_repeat("\x01", 10)));
});

it('should reject a wif with an invalid compression flag', function () {
    $this->expectException(InvalidArgumentException::class);

    TestClass::fromWif(\ArkEcosystem\Crypto\Utils\Base58::encodeCheck("\xaa".str_repeat("\x01", 32)."\x02"));
});

it('should reject a malformed private key', function () {
    $this->expectException(InvalidArgumentException::class);

    TestClass::fromHex('not-a-private-key');
});

it('should reject a private key outside the curve order', function () {
    $this->expectException(InvalidArgumentException::class);

    TestClass::fromHex(str_repeat('f', 64));
});

it('should reject a zero private key', function () {
    $this->expectException(InvalidArgumentException::class);

    TestClass::fromHex(str_repeat('0', 64));
});

it('should sign deterministically with a low s value', function () {
    $privateKey = TestClass::fromPassphrase($this->passphrase);
    $hash       = hash('sha256', 'Hello World', true);

    $signature = $privateKey->sign($hash);

    $this->assertSame($signature->getHex(), $privateKey->sign(new Bytes($hash))->getHex());
    $this->assertSame($this->getFixture('message-v2')['data']['signature'], $signature->getHex());
    $this->assertTrue($privateKey->getPublicKey()->verify($hash, $signature));
});
