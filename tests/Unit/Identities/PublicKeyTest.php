<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Unit\Identities;

use ArkEcosystem\Crypto\Identities\PublicKey;

it('should get the public key from passphrase', function () {
    $fixture = $this->getFixture('identity');

    $actual = PublicKey::fromPassphrase($fixture['passphrase']);

    expect($actual->publicKey)->toBe($fixture['data']['publicKey']);
});

it('should get the public key from hex', function () {
    $fixture = $this->getFixture('identity');

    $actual = PublicKey::fromHex($fixture['data']['publicKey']);

    expect($actual->publicKey)->toBe($fixture['data']['publicKey']);
});
