<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;
use ArkEcosystem\Crypto\Utils\Address as TestClass;

test('it should validate the address', function () {
    $fixture = $this->getFixture('identity');

    $actual = TestClass::validate($fixture['data']['address']);

    expect($actual)->toBeTrue();
});

test('it should fail to validate the address', function () {
    $actual = TestClass::validate('invalid');

    expect($actual)->toBeFalse();
});

it('should convert to hex string', function () {
    $fixture = $this->getFixture('identity');

    $actual = TestClass::toBufferHexString($fixture['data']['address']);

    expect($actual)->toBe(substr($fixture['data']['address'], 2));
});

it('should extract address from a byte buffer', function () {
    $fixture = $this->getFixture('identity');

    $actual = TestClass::fromByteBuffer(ByteBuffer::fromHex(substr($fixture['data']['address'], 2)));

    expect($actual)->toBe($fixture['data']['address']);
});

it('should convert to checksum address and cache the result', function () {
    $address  = '0xb693449adda7efc015d87944eae8b7c37eb1690a';
    $expected = '0xb693449AdDa7EFc015D87944EAE8b7C37EB1690A';

    $reflector = new ReflectionClass(TestClass::class);
    $cache     = $reflector->getProperty('cache');
    $cache->setAccessible(true);
    $cache->setValue(null, []);

    $actual = TestClass::toChecksumAddress($address);

    expect($actual)->toBe($expected);
    expect($cache->getValue())->toHaveCount(1);
    expect($cache->getValue())->toHaveKey($address);

    $cached = TestClass::toChecksumAddress($address);

    expect($cached)->toBe($expected);
    expect($cache->getValue())->toHaveCount(1);
});

it('should accept a correctly checksummed address', function () {
    expect(TestClass::hasValidChecksum('0x75545540230d5c3BEf023202d23CB74cFA723376'))->toBeTrue();
});

it('should accept an all-lowercase address without a checksum', function () {
    expect(TestClass::hasValidChecksum('0x75545540230d5c3bef023202d23cb74cfa723376'))->toBeTrue();
});

it('should reject an address with a wrong checksum', function () {
    expect(TestClass::hasValidChecksum('0x75545540230d5c3bEf023202d23CB74cFA723376'))->toBeFalse()
        ->and(TestClass::hasValidChecksum('0x75545540230D5C3BEF023202D23CB74CFA723376'))->toBeFalse();
});
