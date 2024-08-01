<?php

declare(strict_types=1);



namespace ArkEcosystem\Tests\Crypto\Unit\Managers;

use ArkEcosystem\Crypto\Configuration\Network;
use ArkEcosystem\Crypto\Networks\AbstractNetwork;
use ArkEcosystem\Crypto\Networks\Devnet;
use ArkEcosystem\Crypto\Networks\Mainnet;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
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
