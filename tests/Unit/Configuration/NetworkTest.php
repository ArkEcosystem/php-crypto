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

it('should get the network', function () {
    $actual = Network::get();

    $this->assertInstanceOf(AbstractNetwork::class, $actual);
});

it('should set the network', function () {
    Network::set(Mainnet::new());

    $actual = Network::get();
    $this->assertInstanceOf(Mainnet::class, $actual);

    Network::set(Devnet::new());

    $actual = Network::get();
    $this->assertInstanceOf(Devnet::class, $actual);
});
