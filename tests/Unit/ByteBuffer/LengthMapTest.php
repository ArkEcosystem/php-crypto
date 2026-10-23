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

namespace ArkEcosystem\Tests\Crypto;

use ArkEcosystem\Crypto\ByteBuffer\LengthMap;

it('should get the length for string', function () {
    $this->assertSame(33, LengthMap::get('a33'));
});

it('should get the length for float', function () {
    $this->assertSame(33, LengthMap::get('f33'));
});

it('should get the length for double', function () {
    $this->assertSame(33, LengthMap::get('d33'));
});

it('should get the length for hex with low nibble', function () {
    $this->assertSame(33, LengthMap::get('h66'));
});

it('should get the length for hex with high nibble', function () {
    $this->assertSame(33, LengthMap::get('H66'));
});

it('should get the length from the array', function () {
    $this->assertSame(1, LengthMap::get('C'));
});

it('should throw for invalid type', function () {
    $this->expectException(\InvalidArgumentException::class);

    LengthMap::get('_INVALID_');
});
