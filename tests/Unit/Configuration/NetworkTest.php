<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Unit\Managers;

use ArkEcosystem\Crypto\Configuration\Network;
use ArkEcosystem\Crypto\Networks\AbstractNetwork;
use ArkEcosystem\Crypto\Networks\Mainnet;
use ArkEcosystem\Crypto\Networks\Testnet;

/*
 * @covers \ArkEcosystem\Crypto\Configuration\Network
 */

it('should get the network', function () {
    $actual = Network::get();

    expect($actual)->toBeInstanceOf(AbstractNetwork::class);
});

it('should set the network', function () {
    Network::set(Mainnet::new());

    $actual = Network::get();
    expect($actual)->toBeInstanceOf(Mainnet::class);

    Network::set(Testnet::new());

    $actual = Network::get();
    expect($actual)->toBeInstanceOf(Testnet::class);
});
