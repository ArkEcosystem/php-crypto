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

use ArkEcosystem\Crypto\Networks\AbstractNetwork;
use ArkEcosystem\Crypto\Networks\Devnet;
use ArkEcosystem\Crypto\Networks\Mainnet;
use ArkEcosystem\Crypto\Networks\Testnet;

it('should expose the version bytes', function () {
    $network = Mainnet::new();

    $this->assertSame(23, $network->version());
    $this->assertSame('17', $network->getAddressByte());
    $this->assertSame('00', $network->getP2shByte());
    $this->assertSame('aa', $network->getPrivByte());
    $this->assertSame('46090600', $network->getHDPubByte());
    $this->assertSame('46089520', $network->getHDPrivByte());
});

it('should create a new instance', function () {
    $this->assertInstanceOf(Devnet::class, Devnet::new());
    $this->assertSame(30, Devnet::new()->version());
});

it('should define the epoch and public key hash of each network', function (AbstractNetwork $network, string $epoch, int $pubKeyHash) {
    $this->assertSame($epoch, $network->epoch());
    $this->assertSame($pubKeyHash, $network->pubKeyHash());
})->with([
    'mainnet' => [Mainnet::new(), '2017-03-21T13:00:00.000Z', 23],
    'devnet'  => [Devnet::new(), '2017-03-21T13:00:00.000Z', 30],
    'testnet' => [Testnet::new(), '2017-03-21T13:00:00.000Z', 23],
]);
