<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\Transactions\Builder\TransferBuilder;
use ArkEcosystem\Crypto\Utils\UnitConverter;

it('should sign it with a passphrase', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'transfer');

    $builder = TransferBuilder::new()
        ->gasPrice(UnitConverter::parseUnits($fixture['data']['gasPrice'], 'wei'))
        ->gasLimit(UnitConverter::parseUnits($fixture['data']['gasLimit'], 'wei'))
        ->nonce($fixture['data']['nonce'])
        ->to($fixture['data']['to'])
        ->value(UnitConverter::parseUnits($fixture['data']['value'], 'wei'))
        ->sign($this->passphrase);

    expect((string) $builder->transaction->data['gasPrice'])->toBe($fixture['data']['gasPrice']);
    expect((string) $builder->transaction->data['gasLimit'])->toBe($fixture['data']['gasLimit']);
    expect($builder->transaction->data['nonce'])->toBe($fixture['data']['nonce']);
    expect($builder->transaction->data['to'])->toBe($fixture['data']['to']);
    expect((string) $builder->transaction->data['value'])->toBe((string) $fixture['data']['value']);
    expect($builder->transaction->data['v'])->toBe($fixture['data']['v']);
    expect($builder->transaction->data['r'])->toBe($fixture['data']['r']);
    expect($builder->transaction->data['s'])->toBe($fixture['data']['s']);

    expect($builder->transaction->serialize()->getHex())->toBe($fixture['serialized']);

    expect($builder->transaction->data['hash'])->toBe($fixture['data']['hash']);

    expect($builder->verify())->toBeTrue();
});

it('should sign with a second passphrase', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'transfer-legacy-second-signature');

    $builder = TransferBuilder::new()
        ->gasPrice(UnitConverter::parseUnits($fixture['data']['gasPrice'], 'wei'))
        ->gasLimit(UnitConverter::parseUnits($fixture['data']['gasLimit'], 'wei'))
        ->nonce($fixture['data']['nonce'])
        ->to($fixture['data']['to'])
        ->value(UnitConverter::parseUnits($fixture['data']['value'], 'wei'))
        ->legacySecondSign($this->passphrase, $this->secondPassphrase);

    expect((string) $builder->transaction->data['gasPrice'])->toBe((string) $fixture['data']['gasPrice']);
    expect((string) $builder->transaction->data['gasLimit'])->toBe((string) $fixture['data']['gasLimit']);
    expect($builder->transaction->data['nonce'])->toBe($fixture['data']['nonce']);
    expect($builder->transaction->data['to'])->toBe($fixture['data']['to']);
    expect((string) $builder->transaction->data['value'])->toBe((string) $fixture['data']['value']);
    expect($builder->transaction->data['v'])->toBe($fixture['data']['v']);
    expect($builder->transaction->data['r'])->toBe($fixture['data']['r']);
    expect($builder->transaction->data['s'])->toBe($fixture['data']['s']);
    expect($builder->transaction->data['legacySecondSignature'])->toBe($fixture['data']['legacySecondSignature']);

    expect($builder->transaction->serialize()->getHex())->toBe($fixture['serialized']);

    expect($builder->transaction->data['hash'])->toBe($fixture['data']['hash']);

    expect($builder->verify())->toBeTrue();
});

it('should handle large amounts', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'transfer-large-amount');

    $builder = TransferBuilder::new()
        ->gasPrice(UnitConverter::parseUnits($fixture['data']['gasPrice'], 'wei'))
        ->gasLimit(UnitConverter::parseUnits($fixture['data']['gasLimit'], 'wei'))
        ->nonce($fixture['data']['nonce'])
        ->to($fixture['data']['to'])
        ->value(UnitConverter::parseUnits($fixture['data']['value'], 'wei'))
        ->sign($this->passphrase);

    expect((string) $builder->transaction->data['gasPrice'])->toBe($fixture['data']['gasPrice']);
    expect((string) $builder->transaction->data['gasLimit'])->toBe($fixture['data']['gasLimit']);
    expect($builder->transaction->data['nonce'])->toBe($fixture['data']['nonce']);
    expect($builder->transaction->data['to'])->toBe($fixture['data']['to']);
    expect((string) $builder->transaction->data['value'])->toBe((string) $fixture['data']['value']);
    expect($builder->transaction->data['v'])->toBe($fixture['data']['v']);
    expect($builder->transaction->data['r'])->toBe($fixture['data']['r']);
    expect($builder->transaction->data['s'])->toBe($fixture['data']['s']);

    expect($builder->transaction->serialize()->getHex())->toBe($fixture['serialized']);

    expect($builder->transaction->data['hash'])->toBe($fixture['data']['hash']);

    expect($builder->verify())->toBeTrue();
});

it('should handle unit converter', function () {
    $builder = TransferBuilder::new()
        ->gasPrice(UnitConverter::parseUnits(5, 'gwei'))
        ->gasLimit(UnitConverter::parseUnits(0.1, 'gwei'))
        ->nonce('1')
        ->to($this->address)
        ->value(UnitConverter::parseUnits(10, 'ark'))
        ->sign($this->passphrase);

    expect((string) $builder->transaction->data['gasPrice'])->toBe('5000000000');
    expect($builder->transaction->data['nonce'])->toBe('1');
    expect((string) $builder->transaction->data['gasLimit'])->toBe('100000000');
    expect((string) $builder->transaction->data['value'])->toBe('10000000000000000000');

    expect($builder->verify())->toBeTrue();
});

it('should convert to json when casting to string', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'transfer');

    $builder = TransferBuilder::new()
        ->gasPrice(UnitConverter::parseUnits($fixture['data']['gasPrice'], 'wei'))
        ->gasLimit(UnitConverter::parseUnits($fixture['data']['gasLimit'], 'wei'))
        ->nonce($fixture['data']['nonce'])
        ->to($fixture['data']['to'])
        ->value(UnitConverter::parseUnits($fixture['data']['value'], 'wei'))
        ->sign($this->passphrase);

    expect((string) $builder)->toBe($builder->toJson());
});

it('should convert to an array', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'transfer');

    $builder = TransferBuilder::new()
        ->gasPrice(UnitConverter::parseUnits($fixture['data']['gasPrice'], 'wei'))
        ->gasLimit(UnitConverter::parseUnits($fixture['data']['gasLimit'], 'wei'))
        ->nonce($fixture['data']['nonce'])
        ->to($fixture['data']['to'])
        ->value(UnitConverter::parseUnits($fixture['data']['value'], 'wei'))
        ->sign($this->passphrase);

    expect($builder->toArray())->toBe([
        'gasPrice'        => $builder->transaction->data['gasPrice'],
        'gasLimit'        => $builder->transaction->data['gasLimit'],
        'hash'            => $builder->transaction->data['hash'],
        'nonce'           => $builder->transaction->data['nonce'],
        'senderPublicKey' => $builder->transaction->data['senderPublicKey'],
        'to'              => $fixture['data']['to'],
        'value'           => $builder->transaction->data['value'],
        'data'            => '',
        'r'               => $builder->transaction->data['r'],
        's'               => $builder->transaction->data['s'],
        'v'               => $builder->transaction->data['v'],
    ]);
});
