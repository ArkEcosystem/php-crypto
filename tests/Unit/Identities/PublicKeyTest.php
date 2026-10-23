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
use ArkEcosystem\Crypto\Identities\PublicKey as TestClass;
use InvalidArgumentException;

it('should get the public key from passphrase', function () {
    $fixture = $this->getFixture('identity');

    $actual = TestClass::fromPassphrase($fixture['passphrase']);

    $this->assertSame($fixture['data']['publicKey'], $actual->getHex());
});

it('should get the public key from a multi signature asset', function () {
    $actual = TestClass::fromMultiSignatureAsset(3, [
        '02fb3def2593a00c5b84620addf28ff21bac452bd71a37d4d8e24f301683a81b56',
        '02bc9f661fcc8abca65fe9aff4614036867b7fdcc5730085ccc5cb854664d0194b',
        '03c44c6b6cc9893ae21ca606712fd0f6f03c41ce81c4f6ce5a640f4b0b82ec1ce0',
        '020300039e973baf5e46b945777cfae330d6392cdb039b1cebc5c3382d421166c3',
        '03b050073621b9b5caec9461d44d6bcf21a858c47dd88230ce723e25c1bc75c219',
    ]);

    $this->assertSame(
        '03da05c1c1d4f9c6bda13695b2f29fbc65d9589edc070fc61fe97974be3e59c14e',
        $actual->getHex()
    );
});

it('should get the public key from hex', function () {
    $fixture = $this->getFixture('identity');

    $actual = TestClass::fromHex($fixture['data']['publicKey']);

    $this->assertSame($fixture['data']['publicKey'], $actual->getHex());
});

it('should get the public key from bytes', function () {
    $fixture = $this->getFixture('identity');

    $actual = TestClass::fromHex(Bytes::fromHex($fixture['data']['publicKey']));

    $this->assertSame($fixture['data']['publicKey'], $actual->getHex());
    $this->assertSame($fixture['data']['publicKey'], $actual->getBuffer()->getHex());
    $this->assertSame(hex2bin($fixture['data']['publicKey']), $actual->getBinary());
});

it('should compress an uncompressed public key', function () {
    $fixture = $this->getFixture('identity');

    $uncompressed = TestClass::fromHex($fixture['data']['publicKey']);
    $point        = \ArkEcosystem\Crypto\Utils\Secp256k1::curve()->keyFromPublic($fixture['data']['publicKey'], 'hex')->getPublic(false, 'hex');

    $this->assertSame($uncompressed->getHex(), TestClass::fromHex($point)->getHex());
});

it('should reject a malformed public key', function () {
    $this->expectException(InvalidArgumentException::class);

    TestClass::fromHex('not-a-public-key');
});

it('should reject a public key that is not on the curve', function () {
    $this->expectException(InvalidArgumentException::class);

    TestClass::fromHex('02'.str_pad('5', 64, '0', STR_PAD_LEFT));
});

it('should reject a public key with an invalid prefix', function () {
    $this->expectException(InvalidArgumentException::class);

    TestClass::fromHex('05'.str_repeat('1', 64));
});

it('should not verify invalid signatures', function () {
    $publicKey = TestClass::fromHex($this->getFixture('identity')['data']['publicKey']);
    $hash      = hash('sha256', 'Hello World', true);

    $this->assertFalse($publicKey->verify($hash, 'not-a-signature'));
    $this->assertFalse($publicKey->verify($hash, 'deadbeef'));
    $this->assertFalse($publicKey->verify(hash('sha256', 'Other', true), $this->getFixture('message-v2')['data']['signature']));
});
