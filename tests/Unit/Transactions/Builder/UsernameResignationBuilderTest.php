<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\Transactions\Builder\UsernameResignationBuilder;
use ArkEcosystem\Crypto\Utils\UnitConverter;

it('should sign it with a passphrase', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'username-resignation');

    $builder = UsernameResignationBuilder::new()
        ->gasPrice(UnitConverter::parseUnits($fixture['data']['gasPrice'], 'wei'))
        ->gasLimit(UnitConverter::parseUnits($fixture['data']['gasLimit'], 'wei'))
        ->nonce($fixture['data']['nonce'])
        ->sign($this->passphrase);

    expect((string) $builder->transaction->data['gasPrice'])->toBe((string) $fixture['data']['gasPrice']);
    expect($builder->transaction->data['nonce'])->toBe($fixture['data']['nonce']);
    expect((string) $builder->transaction->data['gasLimit'])->toBe((string) $fixture['data']['gasLimit']);
    expect($builder->transaction->data['v'])->toBe($fixture['data']['v']);
    expect($builder->transaction->data['r'])->toBe($fixture['data']['r']);
    expect($builder->transaction->data['s'])->toBe($fixture['data']['s']);

    expect($builder->transaction->serialize()->getHex())->toBe($fixture['serialized']);

    expect($builder->transaction->data['hash'])->toBe($fixture['data']['hash']);

    expect($builder->verify())->toBeTrue();
});

it('should convert to json when casting to string', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'username-resignation');

    $builder = UsernameResignationBuilder::new()
        ->gasPrice(UnitConverter::parseUnits($fixture['data']['gasPrice'], 'wei'))
        ->gasLimit(UnitConverter::parseUnits($fixture['data']['gasLimit'], 'wei'))
        ->nonce($fixture['data']['nonce'])
        ->sign($this->passphrase);

    expect((string) $builder)->toBe($builder->toJson());
});

it('should convert to an array', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'unvote');

    $builder = UsernameResignationBuilder::new()
        ->gasPrice(UnitConverter::parseUnits($fixture['data']['gasPrice'], 'wei'))
        ->gasLimit(UnitConverter::parseUnits($fixture['data']['gasLimit'], 'wei'))
        ->nonce($fixture['data']['nonce'])
        ->sign($this->passphrase);

    expect($builder->toArray())->toBe([
        'gasPrice'        => $builder->transaction->data['gasPrice'],
        'gasLimit'        => $builder->transaction->data['gasLimit'],
        'hash'            => $builder->transaction->data['hash'],
        'nonce'           => $builder->transaction->data['nonce'],
        'senderPublicKey' => $builder->transaction->data['senderPublicKey'],
        'to'              => $builder->transaction->data['to'],
        'value'           => $builder->transaction->data['value'],
        'data'            => $builder->transaction->data['data'],
        'r'               => $builder->transaction->data['r'],
        's'               => $builder->transaction->data['s'],
        'v'               => $builder->transaction->data['v'],
    ]);
});
