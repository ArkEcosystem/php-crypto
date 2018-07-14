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

namespace ArkEcosystem\Tests\Crypto\Networks;

use ArkEcosystem\Tests\Crypto\TestCase;
use BitWasp\Bitcoin\Network\Network;

/**
 * This is the devnet network test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @coversNothing
 */
class NetworkTestCase extends TestCase
{
    /** @test */
    public function it_should_get_version()
    {
        $actual = $this->getTestSubject()::getVersion();

        $this->assertSame($actual, $this->version);
    }

    /** @test */
    public function it_should_get_nethash()
    {
        $actual = $this->getTestSubject()::getNethash();

        $this->assertSame($actual, $this->nethash);
    }

    /** @test */
    public function it_should_get_wif()
    {
        $actual = $this->getTestSubject()::getWif();

        $this->assertSame($actual, $this->wif);
    }

    /** @test */
    public function it_should_get_wif_byte()
    {
        $actual = $this->getTestSubject()::getWifByte();

        $this->assertSame($actual, $this->wifByte);
    }

    /** @test */
    public function it_should_get_factory()
    {
        $this->assertInstanceOf(Network::class, $this->getTestSubject()::getFactory());
    }
}
