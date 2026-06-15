<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\Transactions\Builder\EvmCallBuilder;
use ArkEcosystem\Crypto\Utils\UnitConverter;

it('should sign it with a passphrase', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'evm-sign');

    $builder = EvmCallBuilder::new()
        ->gasPrice(UnitConverter::parseUnits($fixture['data']['gasPrice'], 'wei'))
        ->gasLimit(UnitConverter::parseUnits($fixture['data']['gasLimit'], 'wei'))
        ->nonce($fixture['data']['nonce'])
        ->payload($fixture['data']['data'])
        ->to('0xE536720791A7DaDBeBdBCD8c8546fb0791a11901')
        ->sign($this->passphrase);

    expect($builder->verify())->toBeTrue();
});

it('should convert to json when casting to string', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'evm-sign');

    $builder = EvmCallBuilder::new()
        ->gasPrice(UnitConverter::parseUnits($fixture['data']['gasPrice'], 'wei'))
        ->gasLimit(UnitConverter::parseUnits($fixture['data']['gasLimit'], 'wei'))
        ->nonce($fixture['data']['nonce'])
        ->payload($fixture['data']['data'])
        ->to('0xE536720791A7DaDBeBdBCD8c8546fb0791a11901')
        ->sign($this->passphrase);

    expect((string) $builder)->toBe($builder->toJson());
});

it('should convert to an array', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'evm-sign');

    $builder = EvmCallBuilder::new()
        ->gasPrice(UnitConverter::parseUnits($fixture['data']['gasPrice'], 'wei'))
        ->gasLimit(UnitConverter::parseUnits($fixture['data']['gasLimit'], 'wei'))
        ->nonce($fixture['data']['nonce'])
        ->payload($fixture['data']['data'])
        ->to('0xE536720791A7DaDBeBdBCD8c8546fb0791a11901')
        ->sign($this->passphrase);

    expect($builder->toArray())->toBe([
        'gasPrice'        => $builder->transaction->data['gasPrice'],
        'gasLimit'        => $builder->transaction->data['gasLimit'],
        'hash'            => $builder->transaction->data['hash'],
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
