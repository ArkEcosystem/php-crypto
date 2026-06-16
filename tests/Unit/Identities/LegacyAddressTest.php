<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Unit\Identities;

use ArkEcosystem\Crypto\Identities\LegacyAddress;
use ArkEcosystem\Crypto\Identities\PrivateKey;

it('should get the address from passphrase', function () {
    $fixture = $this->getFixture('legacy-identity');

    $actual = LegacyAddress::fromPassphrase($fixture['passphrase'], $fixture['pubKeyHash']);

    expect($actual)->toBe($fixture['data']['address']);
});

it('should get the address from public key', function () {
    $fixture = $this->getFixture('legacy-identity');

    $actual = LegacyAddress::fromPublicKey($fixture['data']['publicKey'], $fixture['pubKeyHash']);

    expect($actual)->toBe($fixture['data']['address']);
});

it('should get the address from private key', function () {
    $fixture = $this->getFixture('legacy-identity');

    $privateKey = PrivateKey::fromHex($fixture['data']['privateKey']);

    $actual = LegacyAddress::fromPrivateKey($privateKey, $fixture['pubKeyHash']);

    expect($actual)->toBe($fixture['data']['address']);
});

it('should validate the address', function () {
    $fixture = $this->getFixture('legacy-identity');

    $actual = LegacyAddress::validate($fixture['data']['address'], $fixture['pubKeyHash']);

    expect($actual)->toBeTrue();
});

it('should fail to validate address with incorrect pubKeyHash', function () {
    $fixture = $this->getFixture('legacy-identity');

    $actual = LegacyAddress::validate($fixture['data']['address'], 32);

    expect($actual)->toBeFalse();
});

it('should fail to validate an invalid address', function () {
    $fixture = $this->getFixture('legacy-identity');

    $actual = LegacyAddress::validate('D2WFnqYDRiFkSf4ezzWRt3jCsUp2sRmDMifwd', $fixture['pubKeyHash']);

    expect($actual)->toBeFalse();
});

it('should fail to validate address with decoding error', function () {
    $actual = LegacyAddress::validate('invalid', 30);

    expect($actual)->toBeFalse();
});
