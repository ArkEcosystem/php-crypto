<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Unit\Identities;

use ArkEcosystem\Crypto\Identities\PrivateKey;

it('should get the private key from passphrase', function () {
    $fixture = $this->getFixture('identity');

    $actual = PrivateKey::fromPassphrase($fixture['passphrase']);

    expect($actual->getHex())->toBe($fixture['data']['privateKey']);
});

it('should get the private key from hex', function () {
    $fixture = $this->getFixture('identity');

    $actual = PrivateKey::fromHex($fixture['data']['privateKey']);

    expect($actual->getHex())->toBe($fixture['data']['privateKey']);
});

it('should get the private key from wif', function () {
    $fixture = $this->getFixture('identity');

    $actual = PrivateKey::fromWif($fixture['data']['wif']);

    expect($actual->getHex())->toBe($fixture['data']['privateKey']);
});
