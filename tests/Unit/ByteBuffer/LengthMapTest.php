<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto;

use ArkEcosystem\Crypto\ByteBuffer\LengthMap;
use InvalidArgumentException;

it('should get the length for string', function () {
    expect(LengthMap::get('a33'))->toBe(33);
});

it('should get the length for float', function () {
    expect(LengthMap::get('f33'))->toBe(33);
});

it('should get the length for double', function () {
    expect(LengthMap::get('d33'))->toBe(33);
});

it('should get the length for hex with low nibble', function () {
    expect(LengthMap::get('h66'))->toBe(33);
});

it('should get the length for hex with high nibble', function () {
    expect(LengthMap::get('H66'))->toBe(33);
});

it('should get the length from the array', function () {
    expect(LengthMap::get('C'))->toBe(1);
});

it('should throw for invalid type', function () {
    expect(fn () => LengthMap::get('_INVALID_'))->toThrow(InvalidArgumentException::class);
});
