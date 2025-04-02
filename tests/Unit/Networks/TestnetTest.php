<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Unit\Networks;

use ArkEcosystem\Crypto\Networks\Testnet;

/*
 * @covers \ArkEcosystem\Crypto\Networks\Testnet
 */

$epoch = '2017-03-21T13:00:00.000Z';

/**
 * @todo: adjust the value of $chainId to match the actual value
 */
$chainId = 10000;

beforeEach(function () use ($epoch, $chainId) {
    $this->epoch   = $epoch;
    $this->chainId = $chainId;
    $this->network = Testnet::new();
});

it('should get epoch', function () {
    $actual = $this->network->epoch();

    expect($actual)->toBe($this->epoch);
});

it('should get chain id', function () {
    $actual = $this->network->chainId();

    expect($actual)->toBe($this->chainId);
});
