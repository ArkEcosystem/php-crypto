<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\Transactions\Builder\EvmCallBuilder;
use ArkEcosystem\Crypto\Utils\UnitConverter;

it('should sign it with a passphrase', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'evm-sign');

    $builder = EvmCallBuilder::new()
        ->gasPrice(UnitConverter::parseUnits($fixture['data']['gasPrice'], 'wei'))
        ->nonce($fixture['data']['nonce'])
        ->network($fixture['data']['network'])
        ->payload($fixture['data']['data'])
        ->gas(UnitConverter::parseUnits($fixture['data']['gas'], 'wei'))
        ->to('0xE536720791A7DaDBeBdBCD8c8546fb0791a11901')
        ->sign($this->passphrase);

    expect($builder->verify())->toBeTrue();
});

it('should convert to json when casting to string', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'evm-sign');

    $builder = EvmCallBuilder::new()
        ->gasPrice(UnitConverter::parseUnits($fixture['data']['gasPrice'], 'wei'))
        ->nonce($fixture['data']['nonce'])
        ->network($fixture['data']['network'])
        ->payload($fixture['data']['data'])
        ->gas(UnitConverter::parseUnits($fixture['data']['gas'], 'wei'))
        ->to('0xE536720791A7DaDBeBdBCD8c8546fb0791a11901')
        ->sign($this->passphrase);

    expect((string) $builder)->toBe($builder->toJson());
});

it('should convert to an array', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'evm-sign');

    $builder = EvmCallBuilder::new()
        ->gasPrice(UnitConverter::parseUnits($fixture['data']['gasPrice'], 'wei'))
        ->nonce($fixture['data']['nonce'])
        ->network($fixture['data']['network'])
        ->payload($fixture['data']['data'])
        ->gas(UnitConverter::parseUnits($fixture['data']['gas'], 'wei'))
        ->to('0xE536720791A7DaDBeBdBCD8c8546fb0791a11901')
        ->sign($this->passphrase);

    expect($builder->toArray())->toBe([
        'gasPrice'        => $builder->transaction->data['gasPrice'],
        'network'         => $builder->transaction->data['network'],
        'hash'            => $builder->transaction->data['hash'],
        'gas'             => $builder->transaction->data['gas'],
        'nonce'           => $builder->transaction->data['nonce'],
        'senderPublicKey' => $builder->transaction->data['senderPublicKey'],
        'to'              => '0xE536720791A7DaDBeBdBCD8c8546fb0791a11901',
        'value'           => $builder->transaction->data['value'],
        'data'            => $builder->transaction->data['data'],
        'r'               => $builder->transaction->data['r'],
        's'               => $builder->transaction->data['s'],
        'v'               => $builder->transaction->data['v'],
    ]);
});
