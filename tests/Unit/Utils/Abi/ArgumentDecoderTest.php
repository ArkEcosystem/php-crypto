<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Unit\Utils;

use ArkEcosystem\Crypto\Utils\Abi\ArgumentDecoder;

/*
 * @covers \ArkEcosystem\Crypto\Utils\Abi\ArgumentDecoder
 */

it('should decode address', function () {
    $payload  = '000000000000000000000000512F366D524157BcF734546eB29a6d687B762255';
    $expected = '0x512F366D524157BcF734546eB29a6d687B762255';

    $decoder = new ArgumentDecoder($payload);

    expect($decoder->decodeAddress())->toBe($expected);
});

it('should decode a string', function () {
    $payload = '0000000000000000000000000000000000000000000000000000000000000020000000000000000000000000000000000000000000000000000000000000000474657374';
    $expected = 'test';

    $decoder = new ArgumentDecoder($payload);

    expect($decoder->decodeString())->toBe($expected);
});

it('should decode unsigned int', function () {
    $payload  = '000000000000000000000000000000000000000000000000016345785d8a0000';
    $expected = '100000000000000000';

    $decoder = new ArgumentDecoder($payload);

    expect($decoder->decodeUnsignedInt())->toBe($expected);
});

it('should decode signed int', function () {
    $payload  = '000000000000000000000000000000000000000000000000016345785d8a0000';
    $expected = '100000000000000000';

    $decoder = new ArgumentDecoder($payload);

    expect($decoder->decodeSignedInt())->toBe($expected);
});

it('should decode bool as true', function () {
    $payload  = '0000000000000000000000000000000000000000000000000000000000000001';
    $expected = true;

    $decoder = new ArgumentDecoder($payload);

    expect($decoder->decodeBool())->toBe($expected);
});

it('should decode bool as false', function () {
    $payload  = '0000000000000000000000000000000000000000000000000000000000000000';
    $expected = false;

    $decoder = new ArgumentDecoder($payload);

    expect($decoder->decodeBool())->toBe($expected);
});

it('should handle issue converting hex to binary', function () {
    $decoder = new ArgumentDecoder('invalid');

    $reflectionProperty = new \ReflectionProperty(ArgumentDecoder::class, 'bytes');
    $reflectionProperty->setAccessible(true);

    expect($reflectionProperty->getValue($decoder))->toBe('');
});
