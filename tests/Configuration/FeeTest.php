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

namespace ArkEcosystem\Tests\Crypto\Managers;

use ArkEcosystem\Crypto\Configuration\Fee as FeeConfiguration;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * This is the fee configuration test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @coversNothing
 */
class FeeTest extends TestCase
{
    /** @test */
    public function it_should_get_the_fee()
    {
        $actual = FeeConfiguration::get(0);

        $this->assertSame(10000000, $actual);
    }

    /** @test */
    public function it_should_set_the_fee()
    {
        $actual = FeeConfiguration::get(0);
        $this->assertSame(10000000, $actual);

        FeeConfiguration::set(0, 5);

        $actual = FeeConfiguration::get(0);
        $this->assertSame(5, $actual);
    }
}
