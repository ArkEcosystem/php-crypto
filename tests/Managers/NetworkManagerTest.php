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

use ArkEcosystem\Crypto\Managers\NetworkManager;
use ArkEcosystem\Crypto\Networks\Devnet;
use ArkEcosystem\Crypto\Networks\Mainnet;
use ArkEcosystem\Crypto\Networks\Network;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * This is the config test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @coversNothing
 */
class NetworkManagerTest extends TestCase
{
    /** @test */
    public function it_should_get_the_network()
    {
        $actual = NetworkManager::get();

        $this->assertInstanceOf(Network::class, $actual);
    }

    /** @test */
    public function it_should_set_the_network()
    {
        $actual = NetworkManager::get();
        $this->assertInstanceOf(Mainnet::class, $actual);

        NetworkManager::set(Devnet::create());

        $actual = NetworkManager::get();
        $this->assertInstanceOf(Devnet::class, $actual);
    }
}
