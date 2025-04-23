<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\Transactions\Builder\UnvoteBuilder;
use ArkEcosystem\Crypto\Utils\UnitConverter;

it('should sign it with a passphrase', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'unvote');

    $builder = UnvoteBuilder::new()
        ->gasPrice(UnitConverter::parseUnits($fixture['data']['gasPrice'], 'wei'))
        ->nonce($fixture['data']['nonce'])
        ->network($fixture['data']['network'])
        ->gas(UnitConverter::parseUnits($fixture['data']['gas'], 'wei'))
        ->sign($this->passphrase);

    expect((string) $builder->transaction->data['gasPrice'])->toBe((string) $fixture['data']['gasPrice']);
    expect($builder->transaction->data['nonce'])->toBe($fixture['data']['nonce']);
    expect($builder->transaction->data['network'])->toBe($fixture['data']['network']);
    expect((string) $builder->transaction->data['gas'])->toBe((string) $fixture['data']['gas']);
    expect($builder->transaction->data['v'])->toBe($fixture['data']['v']);
    expect($builder->transaction->data['r'])->toBe($fixture['data']['r']);
    expect($builder->transaction->data['s'])->toBe($fixture['data']['s']);

    expect($builder->transaction->serialize()->getHex())->toBe($fixture['serialized']);

    expect($builder->transaction->data['hash'])->toBe($fixture['data']['hash']);

    expect($builder->verify())->toBeTrue();
});
