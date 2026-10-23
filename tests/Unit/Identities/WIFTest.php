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

namespace ArkEcosystem\Tests\Crypto\Unit\Identities;

use ArkEcosystem\Crypto\Identities\WIF as TestClass;

it('should get the wif from passphrase', function () {
    $fixture = $this->getFixture('identity');

    $actual = TestClass::fromPassphrase($fixture['passphrase']);

    $this->assertSame($fixture['data']['wif'], $actual);
});
