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

use ArkEcosystem\Crypto\Configuration\Network;
use ArkEcosystem\Crypto\Networks\AbstractNetwork;
use ArkEcosystem\Crypto\Networks\Devnet;
use ArkEcosystem\Crypto\Networks\Mainnet;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * This is the network configuration test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @covers \ArkEcosystem\Crypto\Configuration\Network
 */
class NetworkTest extends TestCase
{
    /** @test */
    public function it_should_get_the_network()
    {
        $actual = Network::get();

        $this->assertInstanceOf(AbstractNetwork::class, $actual);
    }

    /** @test */
    public function it_should_set_the_network()
    {
        Network::set(Mainnet::new());

        $actual = Network::get();
        $this->assertInstanceOf(Mainnet::class, $actual);

        Network::set(Devnet::new());

        $actual = Network::get();
        $this->assertInstanceOf(Devnet::class, $actual);
    }
}
