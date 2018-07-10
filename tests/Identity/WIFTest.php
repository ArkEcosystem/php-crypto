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

namespace ArkEcosystem\Tests\Crypto\Identity;

use ArkEcosystem\Crypto\Identity\WIF as TestClass;
use ArkEcosystem\Crypto\Networks\Devnet;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * This is the address test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @coversNothing
 */
class WIFTest extends TestCase
{
    /** @test */
    public function it_should_get_the_wif_from_passphrase()
    {
        $actual = TestClass::fromPassphrase('this is a top secret passphrase', Devnet::new());

        $this->assertSame('SGq4xLgZKCGxs7bjmwnBrWcT4C1ADFEermj846KC97FSv1WFD1dA', $actual);
    }
}
