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

namespace ArkEcosystem\Tests\Crypto\Concerns\Writes;

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;

it('should write float32', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeFloat32(8.0);

    $this->assertSame(4, $buffer->internalSize());
});

it('should write float64', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeFloat64(8.0);

    $this->assertSame(8, $buffer->internalSize());
});

it('should write double', function () {
    $buffer = ByteBuffer::new(1);
    $buffer->writeDouble(8.0);

    $this->assertSame(8, $buffer->internalSize());
});
