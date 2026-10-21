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

use ArkEcosystem\Crypto\Utils\Slot;

it('should get the time', function () {
    $actual = Slot::time();

    $this->assertIsInt($actual);
});

it('should get the epoch', function () {
    $actual = Slot::epoch();

    $this->assertIsInt($actual);
});
