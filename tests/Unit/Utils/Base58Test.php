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

use ArkEcosystem\Crypto\Utils\Base58;
use InvalidArgumentException;

it('should encode and decode binary', function () {
    $this->assertSame('StV1DL6CwTryKyV', Base58::encode('hello world'));
    $this->assertSame('hello world', Base58::decode('StV1DL6CwTryKyV'));
});

it('should keep leading zero bytes', function () {
    $this->assertSame('11', Base58::encode("\0\0"));
    $this->assertSame("\0\0", Base58::decode('11'));
    $this->assertSame('112', Base58::encode("\0\0\x01"));
    $this->assertSame("\0\0\x01", Base58::decode('112'));
});

it('should handle empty values', function () {
    $this->assertSame('', Base58::encode(''));
    $this->assertSame('', Base58::decode(''));
});

it('should reject invalid characters', function () {
    $this->expectException(InvalidArgumentException::class);

    Base58::decode('0OIl');
});

it('should round trip with a checksum', function () {
    $this->assertSame("\x1e".str_repeat("\xab", 20), Base58::decodeCheck(Base58::encodeCheck("\x1e".str_repeat("\xab", 20))));
});

it('should decode an address with a checksum', function () {
    $this->assertSame(
        '1e'.bin2hex(hash('ripemd160', hex2bin('034151a3ec46b5670a682b0a63394f863587d1bc97483b1b6c70eb58e7f0aed192'), true)),
        bin2hex(Base58::decodeCheck('D61mfSggzbvQgTUe6JhYKH2doHaqJ3Dyib'))
    );
});

it('should reject an invalid checksum', function () {
    $this->expectException(InvalidArgumentException::class);

    Base58::decodeCheck('D61mfSggzbvQgTUe6JhYKH2doHaqJ3Dyic');
});

it('should reject values too short for a checksum', function () {
    $this->expectException(InvalidArgumentException::class);

    Base58::decodeCheck('2');
});
