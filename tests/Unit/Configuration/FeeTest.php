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

namespace ArkEcosystem\Tests\Crypto\Unit\Managers;

use ArkEcosystem\Crypto\Configuration\Fee;

it('should get the fee', function () {
    $actual = Fee::get(0);

    $this->assertSame('10000000', $actual);
});

it('should set the fee', function () {
    $actual = Fee::get(0);
    $this->assertSame('10000000', $actual);

    Fee::set(0, '5');

    $actual = Fee::get(0);
    $this->assertSame('5', $actual);
});
