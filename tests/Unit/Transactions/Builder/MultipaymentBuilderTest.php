<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\Transactions\Builder\MultipaymentBuilder;
use ArkEcosystem\Crypto\Utils\UnitConverter;

it('should sign it with a passphrase', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'multipayment');

    $builder = MultipaymentBuilder::new()
        ->gasPrice(UnitConverter::parseUnits($fixture['data']['gasPrice'], 'wei'))
        ->gasLimit(UnitConverter::parseUnits($fixture['data']['gasLimit'], 'wei'))
        ->nonce($fixture['data']['nonce'])
        ->pay('0x6f0182a0cc707b055322ccf6d4cb6a5aff1aeb22', UnitConverter::parseUnits('100000', 'wei'))
        ->pay('0xc3bbe9b1cee1ff85ad72b87414b0e9b7f2366763', UnitConverter::parseUnits('200000', 'wei'))
        ->sign($this->passphrase);

    expect((string) $builder->transaction->data['gasPrice'])->toBe((string) $fixture['data']['gasPrice']);
    expect((string) $builder->transaction->data['gasLimit'])->toBe((string) $fixture['data']['gasLimit']);
    expect($builder->transaction->data['nonce'])->toBe($fixture['data']['nonce']);
    expect($builder->transaction->data['v'])->toBe($fixture['data']['v']);
    expect($builder->transaction->data['r'])->toBe($fixture['data']['r']);
    expect($builder->transaction->data['s'])->toBe($fixture['data']['s']);

    expect($builder->transaction->serialize()->getHex())->toBe($fixture['serialized']);

    expect($builder->transaction->data['hash'])->toBe($fixture['data']['hash']);

    expect($builder->verify())->toBeTrue();
});

it('should handle single recipient', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'multipayment-single');

    $builder = MultipaymentBuilder::new()
        ->gasPrice(UnitConverter::parseUnits($fixture['data']['gasPrice'], 'wei'))
        ->gasLimit(UnitConverter::parseUnits($fixture['data']['gasLimit'], 'wei'))
        ->nonce($fixture['data']['nonce'])
        ->pay('0x6f0182a0cc707b055322ccf6d4cb6a5aff1aeb22', UnitConverter::parseUnits('100000', 'wei'))
        ->sign($this->passphrase);

    expect((string) $builder->transaction->data['gasPrice'])->toBe((string) $fixture['data']['gasPrice']);
    expect((string) $builder->transaction->data['gasLimit'])->toBe((string) $fixture['data']['gasLimit']);
    expect($builder->transaction->data['nonce'])->toBe($fixture['data']['nonce']);
    expect($builder->transaction->data['v'])->toBe($fixture['data']['v']);
    expect($builder->transaction->data['r'])->toBe($fixture['data']['r']);
    expect($builder->transaction->data['s'])->toBe($fixture['data']['s']);

    expect($builder->transaction->serialize()->getHex())->toBe($fixture['serialized']);

    expect($builder->transaction->data['hash'])->toBe($fixture['data']['hash']);

    expect($builder->verify())->toBeTrue();
});

it('should handle empty payment', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'multipayment-empty');

    $builder = MultipaymentBuilder::new()
        ->gasPrice(UnitConverter::parseUnits($fixture['data']['gasPrice'], 'wei'))
        ->gasLimit(UnitConverter::parseUnits($fixture['data']['gasLimit'], 'wei'))
        ->nonce($fixture['data']['nonce'])
        ->sign($this->passphrase);

    expect((string) $builder->transaction->data['gasPrice'])->toBe((string) $fixture['data']['gasPrice']);
    expect((string) $builder->transaction->data['gasLimit'])->toBe((string) $fixture['data']['gasLimit']);
    expect($builder->transaction->data['nonce'])->toBe($fixture['data']['nonce']);
    expect($builder->transaction->data['v'])->toBe($fixture['data']['v']);
    expect($builder->transaction->data['r'])->toBe($fixture['data']['r']);
    expect($builder->transaction->data['s'])->toBe($fixture['data']['s']);

    expect($builder->transaction->serialize()->getHex())->toBe($fixture['serialized']);

    expect($builder->transaction->data['hash'])->toBe($fixture['data']['hash']);

    expect($builder->verify())->toBeTrue();
});

// TODO: fix decoder issue first
// it('should be possible to create manual multipayment', function () {
//     $fixture = $this->getTransactionFixture('evm_call', 'multipayment-1');
//
//     $payload = (new AbiEncoder(ContractAbiType::MULTIPAYMENT))->encodeFunctionCall('pay', [['0x8233F6Df6449D7655f4643D2E752DC8D2283fAd5'], ['1000000000000000000']]);
//     $tx = (new Multipayment(['data' => $payload]));
//     $tx->data['nonce'] = $fixture['data']['nonce'];
//     $tx->data['gasLimit'] = $fixture['data']['gasLimit'];
//     $tx->data['gasPrice'] = $fixture['data']['gasPrice'];
//     $tx->sign(PrivateKey::fromPassphrase($this->passphrase));
//
//     expect($tx->verify())->toBeTrue();
// });

it('should convert to json when casting to string', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'multipayment');

    $builder = MultipaymentBuilder::new()
        ->gasPrice(UnitConverter::parseUnits($fixture['data']['gasPrice'], 'wei'))
        ->gasLimit(UnitConverter::parseUnits($fixture['data']['gasLimit'], 'wei'))
        ->nonce($fixture['data']['nonce'])
        ->pay('0x6f0182a0cc707b055322ccf6d4cb6a5aff1aeb22', UnitConverter::parseUnits('100000', 'wei'))
        ->pay('0xc3bbe9b1cee1ff85ad72b87414b0e9b7f2366763', UnitConverter::parseUnits('200000', 'wei'))
        ->sign($this->passphrase);

    expect((string) $builder)->toBe($builder->toJson());
});

it('should convert to an array', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'multipayment');

    $builder = MultipaymentBuilder::new()
        ->gasPrice(UnitConverter::parseUnits($fixture['data']['gasPrice'], 'wei'))
        ->gasLimit(UnitConverter::parseUnits($fixture['data']['gasLimit'], 'wei'))
        ->nonce($fixture['data']['nonce'])
        ->pay('0x6f0182a0cc707b055322ccf6d4cb6a5aff1aeb22', UnitConverter::parseUnits('100000', 'wei'))
        ->pay('0xc3bbe9b1cee1ff85ad72b87414b0e9b7f2366763', UnitConverter::parseUnits('200000', 'wei'))
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
