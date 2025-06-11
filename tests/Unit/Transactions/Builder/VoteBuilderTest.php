<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\Transactions\Builder\VoteBuilder;
use ArkEcosystem\Crypto\Utils\UnitConverter;

it('should sign it with a passphrase', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'vote');

    $builder = VoteBuilder::new()
        ->gasPrice(UnitConverter::parseUnits($fixture['data']['gasPrice'], 'wei'))
        ->nonce($fixture['data']['nonce'])
        ->network($fixture['data']['network'])
        ->vote('0xC3bBE9B1CeE1ff85Ad72b87414B0E9B7F2366763')
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

it('should convert to json when casting to string', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'vote');

    $builder = VoteBuilder::new()
        ->gasPrice(UnitConverter::parseUnits($fixture['data']['gasPrice'], 'wei'))
        ->nonce($fixture['data']['nonce'])
        ->network($fixture['data']['network'])
        ->vote('0xC3bBE9B1CeE1ff85Ad72b87414B0E9B7F2366763')
        ->gas(UnitConverter::parseUnits($fixture['data']['gas'], 'wei'))
        ->sign($this->passphrase);

    expect((string) $builder)->toBe($builder->toJson());
});

it('should convert to an array', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'unvote');

    $builder = VoteBuilder::new()
        ->gasPrice(UnitConverter::parseUnits($fixture['data']['gasPrice'], 'wei'))
        ->nonce($fixture['data']['nonce'])
        ->network($fixture['data']['network'])
        ->vote('0xC3bBE9B1CeE1ff85Ad72b87414B0E9B7F2366763')
        ->gas(UnitConverter::parseUnits($fixture['data']['gas'], 'wei'))
        ->sign($this->passphrase);

    expect($builder->toArray())->toBe([
        'gasPrice' => $builder->transaction->data['gasPrice'],
        'network' => $builder->transaction->data['network'],
        'hash' => $builder->transaction->data['hash'],
        'gas' => $builder->transaction->data['gas'],
        'nonce' => $builder->transaction->data['nonce'],
        'senderPublicKey' => $builder->transaction->data['senderPublicKey'],
        'to' => $builder->transaction->data['to'],
        'value' => $builder->transaction->data['value'],
        'data' => $builder->transaction->data['data'],
        'r' => $builder->transaction->data['r'],
        's' => $builder->transaction->data['s'],
        'v' => $builder->transaction->data['v'],
    ]);
});
