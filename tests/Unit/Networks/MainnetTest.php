<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Unit\Networks;

use ArkEcosystem\Crypto\Networks\Mainnet;

/*
 * @covers \ArkEcosystem\Crypto\Networks\Mainnet
 */

$epoch = '2017-03-21T13:00:00.000Z';
$wif   = 'ba';

/**
 * @todo: adjust the $chainId once known
 */
$chainId = 10000;

beforeEach(function () use ($epoch, $chainId, $wif) {
    $this->chainId = $chainId;
    $this->epoch   = $epoch;
    $this->wif     = $wif;
    $this->network = Mainnet::new();
});

it('should get epoch', function () {
    $actual = $this->network->epoch();

    expect($actual)->toBe($this->epoch);
});

it('should get chain id', function () {
    $actual = $this->network->chainId();

    expect($actual)->toBe($this->chainId);
});

it('should get the wif', function () {
    $actual = $this->network->wif();

    expect($actual)->toBe($this->wif);
});
