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

namespace ArkEcosystem\Tests\Crypto\Unit\Binary;

use ArkEcosystem\Crypto\Binary\Bytes;
use InvalidArgumentException;

it('should expose the bytes as binary hex and size', function () {
    $bytes = new Bytes("\x00\xff");

    $this->assertSame("\x00\xff", $bytes->getBinary());
    $this->assertSame('00ff', $bytes->getHex());
    $this->assertSame(2, $bytes->getSize());
});

it('should create bytes from hex', function () {
    $this->assertSame("\xab\xcd", Bytes::fromHex('ABCD')->getBinary());
    $this->assertSame('', Bytes::fromHex('')->getBinary());
});

it('should reject invalid hex', function () {
    $this->expectException(InvalidArgumentException::class);

    Bytes::fromHex('abc');
});

it('should reject non hex characters', function () {
    $this->expectException(InvalidArgumentException::class);

    Bytes::fromHex('zz');
});
