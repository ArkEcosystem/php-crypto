<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Unit\Identities;

use ArkEcosystem\Crypto\Configuration\Network;
use ArkEcosystem\Crypto\Identities\Address;
use ArkEcosystem\Crypto\Identities\PrivateKey;
use ArkEcosystem\Crypto\Networks\Testnet;

beforeEach(function () {
    Network::set(Testnet::new());
});

it('should get the address from passphrase', function () {
    $fixture = $this->getFixture('identity');

    $actual = Address::fromPassphrase($fixture['passphrase']);

    expect($actual)->toBe($fixture['data']['address']);
});

it('should get the address from public key', function () {
    $fixture = $this->getFixture('identity');

    $actual = Address::fromPublicKey($fixture['data']['publicKey']);

    expect($actual)->toBe($fixture['data']['address']);
});

it('should get the address from private key', function () {
    $fixture = $this->getFixture('identity');

    $privateKey = PrivateKey::fromPassphrase($fixture['passphrase']);

    $actual = Address::fromPrivateKey($privateKey);

    expect($actual)->toBe($fixture['data']['address']);
});

it('should validate an address', function () {
    $fixture = $this->getFixture('identity');

    $actual = Address::validate($fixture['data']['address']);

    expect($actual)->toBeTrue();
});

it('should return false for an invalid address', function () {
    $actual = Address::validate('invalid-address');

    expect($actual)->toBeFalse();
});
