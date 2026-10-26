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
use ArkEcosystem\Crypto\Identities\PrivateKey;
use ArkEcosystem\Crypto\Identities\PublicKey as TestClass;
use ArkEcosystem\Crypto\Utils\Secp256k1;
use BN\BN;
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
    $point        = Secp256k1::curve()->keyFromPublic($fixture['data']['publicKey'], 'hex')->getPublic(false, 'hex');

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

it('should verify a schnorr signature made by ark core and ark vault', function () {
    $publicKey = TestClass::fromHex('034151a3ec46b5670a682b0a63394f863587d1bc97483b1b6c70eb58e7f0aed192');
    $signature = '2521d2ebe067546920bec3becc581d4d8186d4d2431980b34834ad3d3945be6ed32d10b6499307ba499b72448c971516a9e2be96c79cbf2ad5948b06c1ab242a';

    $this->assertTrue($publicKey->verifySchnorr(hash('sha256', 'Hello World', true), $signature));
    $this->assertTrue($publicKey->verifySchnorr(new Bytes(hash('sha256', 'Hello World', true)), Bytes::fromHex($signature)));
    $this->assertTrue($publicKey->verifySchnorrOrECDSA(hash('sha256', 'Hello World', true), $signature));
    $this->assertFalse($publicKey->verifySchnorr(hash('sha256', 'Hello World!', true), $signature));
});

it('should still verify ecdsa signatures when schnorr or ecdsa is accepted', function () {
    $publicKey = TestClass::fromHex('034151a3ec46b5670a682b0a63394f863587d1bc97483b1b6c70eb58e7f0aed192');

    $this->assertTrue($publicKey->verifySchnorrOrECDSA(hash('sha256', 'Hello World', true), $this->getFixture('message-v2')['data']['signature']));
});

it('should reject malformed schnorr signatures', function (string $signature) {
    $publicKey = TestClass::fromHex('034151a3ec46b5670a682b0a63394f863587d1bc97483b1b6c70eb58e7f0aed192');

    $this->assertFalse($publicKey->verifySchnorr(hash('sha256', 'Hello World', true), $signature));
})->with([
    'too short'          => [str_repeat('ab', 63)],
    'not hex'            => [str_repeat('zz', 64)],
    'r outside field'    => [str_repeat('f', 64).str_repeat('1', 64)],
    's outside order'    => [str_repeat('1', 64).str_repeat('f', 64)],
    'tampered s'         => ['2521d2ebe067546920bec3becc581d4d8186d4d2431980b34834ad3d3945be6ed32d10b6499307ba499b72448c971516a9e2be96c79cbf2ad5948b06c1ab242c'],
]);

it('should reject a schnorr signature whose nonce point has the wrong y', function () {
    $privateKey = PrivateKey::fromPassphrase('this is a top secret passphrase');
    $publicKey  = $privateKey->getPublicKey();
    $hash       = hash('sha256', 'Hello World', true);
    $signature  = '2521d2ebe067546920bec3becc581d4d8186d4d2431980b34834ad3d3945be6ed32d10b6499307ba499b72448c971516a9e2be96c79cbf2ad5948b06c1ab242a';

    // s' = 2ed - s makes s'G - eP the negation of R: same x as r, but a y that is not a quadratic residue.
    $n  = Secp256k1::curve()->n;
    $r  = substr($signature, 0, 64);
    $e  = (new BN(hash('sha256', hex2bin($r).$publicKey->getBinary().$hash), 16))->umod($n);
    $d  = new BN($privateKey->getHex(), 16);
    $s  = new BN(substr($signature, 64), 16);
    $s2 = $e->mul($d)->muln(2)->sub($s)->umod($n);

    $this->assertFalse($publicKey->verifySchnorr($hash, $r.str_pad($s2->toString(16), 64, '0', STR_PAD_LEFT)));
});
