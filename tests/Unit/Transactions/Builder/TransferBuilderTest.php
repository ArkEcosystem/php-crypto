<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\Transactions\Builder\TransferBuilder;
use ArkEcosystem\Crypto\Utils\UnitConverter;

it('should sign it with a passphrase', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'transfer');

    $builder = TransferBuilder::new()
        ->gasPrice(UnitConverter::parseUnits($fixture['data']['gasPrice'], 'wei'))
        ->nonce($fixture['data']['nonce'])
        ->network($fixture['data']['network'])
        ->gas(UnitConverter::parseUnits($fixture['data']['gas'], 'wei'))
        ->to($fixture['data']['to'])
        ->value(UnitConverter::parseUnits($fixture['data']['value'], 'wei'))
        ->sign($this->passphrase);

    expect((string) $builder->transaction->data['gasPrice'])->toBe((string) $fixture['data']['gasPrice']);
    expect($builder->transaction->data['nonce'])->toBe($fixture['data']['nonce']);
    expect($builder->transaction->data['network'])->toBe($fixture['data']['network']);
    expect((string) $builder->transaction->data['gas'])->toBe((string) $fixture['data']['gas']);
    expect($builder->transaction->data['to'])->toBe($fixture['data']['to']);
    expect((string) $builder->transaction->data['value'])->toBe((string) $fixture['data']['value']);
    expect($builder->transaction->data['v'])->toBe($fixture['data']['v']);
    expect($builder->transaction->data['r'])->toBe($fixture['data']['r']);
    expect($builder->transaction->data['s'])->toBe($fixture['data']['s']);

    expect($builder->transaction->serialize()->getHex())->toBe($fixture['serialized']);

    expect($builder->transaction->data['id'])->toBe($fixture['data']['id']);

    expect($builder->verify())->toBeTrue();
});

it('should handle large amounts', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'transfer-large-amount');

    $builder = TransferBuilder::new()
        ->gasPrice(UnitConverter::parseUnits($fixture['data']['gasPrice'], 'wei'))
        ->nonce($fixture['data']['nonce'])
        ->network($fixture['data']['network'])
        ->gas(UnitConverter::parseUnits($fixture['data']['gas'], 'wei'))
        ->to($fixture['data']['to'])
        ->value(UnitConverter::parseUnits($fixture['data']['value'], 'wei'))
        ->sign($this->passphrase);

    expect((string) $builder->transaction->data['gasPrice'])->toBe((string) $fixture['data']['gasPrice']);
    expect($builder->transaction->data['nonce'])->toBe($fixture['data']['nonce']);
    expect($builder->transaction->data['network'])->toBe($fixture['data']['network']);
    expect((string) $builder->transaction->data['gas'])->toBe((string) $fixture['data']['gas']);
    expect($builder->transaction->data['to'])->toBe($fixture['data']['to']);
    expect((string) $builder->transaction->data['value'])->toBe((string) $fixture['data']['value']);
    expect($builder->transaction->data['v'])->toBe($fixture['data']['v']);
    expect($builder->transaction->data['r'])->toBe($fixture['data']['r']);
    expect($builder->transaction->data['s'])->toBe($fixture['data']['s']);

    expect($builder->transaction->serialize()->getHex())->toBe($fixture['serialized']);

    expect($builder->transaction->data['id'])->toBe($fixture['data']['id']);

    expect($builder->verify())->toBeTrue();
});

it('should handle unit converter', function () {
    $builder = TransferBuilder::new()
        ->gasPrice(UnitConverter::parseUnits(5, 'gwei'))
        ->nonce('1')
        ->gas(UnitConverter::parseUnits(0.1, 'gwei'))
        ->to($this->address)
        ->value(UnitConverter::parseUnits(10, 'ark'))
        ->sign($this->passphrase);

    expect((string) $builder->transaction->data['gasPrice'])->toBe('5000000000');
    expect($builder->transaction->data['nonce'])->toBe('1');
    expect((string) $builder->transaction->data['gas'])->toBe('100000000');
    expect((string) $builder->transaction->data['value'])->toBe('10000000000000000000');

    expect($builder->verify())->toBeTrue();
});
