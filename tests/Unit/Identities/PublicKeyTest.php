<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Unit\Identities;

use ArkEcosystem\Crypto\Identities\PublicKey;

it('should get the public key from passphrase', function () {
    $fixture = $this->getFixture('identity');

    $actual = PublicKey::fromPassphrase($fixture['passphrase']);

    expect($actual->getHex())->toBe($fixture['data']['publicKey']);
});

it('should get the public key from a multi signature asset', function () {
    $actual = PublicKey::fromMultiSignatureAsset(3, [
        '02fb3def2593a00c5b84620addf28ff21bac452bd71a37d4d8e24f301683a81b56',
        '02bc9f661fcc8abca65fe9aff4614036867b7fdcc5730085ccc5cb854664d0194b',
        '03c44c6b6cc9893ae21ca606712fd0f6f03c41ce81c4f6ce5a640f4b0b82ec1ce0',
        '020300039e973baf5e46b945777cfae330d6392cdb039b1cebc5c3382d421166c3',
        '03b050073621b9b5caec9461d44d6bcf21a858c47dd88230ce723e25c1bc75c219',
    ]);

    expect($actual->getHex())->toBe('03da05c1c1d4f9c6bda13695b2f29fbc65d9589edc070fc61fe97974be3e59c14e');
});

it('should get the public key from hex', function () {
    $fixture = $this->getFixture('identity');

    $actual = PublicKey::fromHex($fixture['data']['publicKey']);

    expect($actual->getHex())->toBe($fixture['data']['publicKey']);
});
