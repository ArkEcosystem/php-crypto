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

namespace ArkEcosystem\Tests\Crypto\Unit\Networks;

use ArkEcosystem\Crypto\Networks\Devnet;
use ArkEcosystem\Crypto\Networks\Mainnet;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * @covers \ArkEcosystem\Crypto\Networks\AbstractNetwork
 */
class AbstractNetworkTest extends TestCase
{
    /** @test */
    public function it_should_expose_the_version_bytes()
    {
        $network = Mainnet::new();

        $this->assertSame(23, $network->version());
        $this->assertSame('17', $network->getAddressByte());
        $this->assertSame('00', $network->getP2shByte());
        $this->assertSame('aa', $network->getPrivByte());
        $this->assertSame('46090600', $network->getHDPubByte());
        $this->assertSame('46089520', $network->getHDPrivByte());
    }

    /** @test */
    public function it_should_create_a_new_instance()
    {
        $this->assertInstanceOf(Devnet::class, Devnet::new());
        $this->assertSame(30, Devnet::new()->version());
    }
}
