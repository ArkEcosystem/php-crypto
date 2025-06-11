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
