<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Unit\Identities;

use ArkEcosystem\Crypto\Helpers;
use ArkEcosystem\Crypto\Identities\PrivateKey;
use BitWasp\Buffertools\Buffer;

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

it('should sign a message', function () {
    $fixture = $this->getFixture('identity');

    $privateKey = PrivateKey::fromWif($fixture['data']['wif']);

    $signature = $privateKey->sign(new Buffer('message'));

    $v = $signature->getRecoveryId();
    $r = Helpers::gmpToHex($signature->getR());
    $s = Helpers::gmpToHex($signature->getS());

    fwrite(STDERR, $signature->getHex().PHP_EOL);

    expect($signature->getHex())->toBeString();
    expect($v)->toBe(0);
    expect($r)->toBeString();
    expect($s)->toBeString();

    expect(dechex($v).$r.$s)->toHaveLength(129);
});
