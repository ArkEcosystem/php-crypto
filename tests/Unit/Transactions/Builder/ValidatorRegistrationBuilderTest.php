<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\Transactions\Builder\ValidatorRegistrationBuilder;
use ArkEcosystem\Crypto\Utils\UnitConverter;

const BLS_PASSPHRASE = 'gold favorite math anchor detect march purpose such sausage crucial reform novel connect misery update episode invite salute barely garbage exclude winner visa cruise';

it('should sign it with a passphrase', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'validator-registration');

    $builder = ValidatorRegistrationBuilder::new()
        ->gasPrice(UnitConverter::parseUnits($fixture['data']['gasPrice'], 'wei'))
        ->gasLimit(UnitConverter::parseUnits($fixture['data']['gasLimit'], 'wei'))
        ->nonce($fixture['data']['nonce'])
        ->validatorPassphrase(BLS_PASSPHRASE)
        ->sign($this->passphrase);

    expect((string) $builder->transaction->data['gasPrice'])->toBe($fixture['data']['gasPrice']);
    expect((string) $builder->transaction->data['gasLimit'])->toBe($fixture['data']['gasLimit']);
    expect($builder->transaction->data['nonce'])->toBe($fixture['data']['nonce']);
    expect($builder->transaction->data['v'])->toBe($fixture['data']['v']);
    expect($builder->transaction->data['r'])->toBe($fixture['data']['r']);
    expect($builder->transaction->data['s'])->toBe($fixture['data']['s']);

    expect($builder->transaction->serialize()->getHex())->toBe($fixture['serialized']);

    expect($builder->transaction->data['hash'])->toBe($fixture['data']['hash']);

    expect($builder->verify())->toBeTrue();
});

it('should convert to json when casting to string', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'validator-registration');

    $builder = ValidatorRegistrationBuilder::new()
        ->gasPrice(UnitConverter::parseUnits($fixture['data']['gasPrice'], 'wei'))
        ->gasLimit(UnitConverter::parseUnits($fixture['data']['gasLimit'], 'wei'))
        ->nonce($fixture['data']['nonce'])
        ->validatorPassphrase(BLS_PASSPHRASE)
        ->sign($this->passphrase);

    expect((string) $builder)->toBe($builder->toJson());
});

it('should set value on the transaction', function () {
    $builder = ValidatorRegistrationBuilder::new()
        ->value(UnitConverter::parseUnits(10, 'ark'));

    expect((string) $builder->transaction->data['value'])->toBe('10000000000000000000');
});

it('should derive correct validatorPublicKey and validatorProof from passphrase', function () {
    $builder = ValidatorRegistrationBuilder::new()
        ->validatorPassphrase(BLS_PASSPHRASE);

    expect($builder->transaction->data['validatorPublicKey'])
        ->toBe('a18dba7811b212bbb2f080d7c69935998ffbe7b38586e2d3e9e12079ea789996d1c69feb158c002aed327f69865be496');

    expect($builder->transaction->data['validatorProof'])
        ->toBe('a124539f9d469919eb57224cc003d9d5b086a27c6de244abf60b74b35fb749fceab7f4c24b983475cddab7d0876de49c000b5c362f5e3ce18d964f5c2d20d4eadcf7cb77a73d8ee4cd87bad10f7ba0824cea6715d1c045b4f93865a2758b7bfe');
});

it('should convert to an array', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'validator-registration');

    $builder = ValidatorRegistrationBuilder::new()
        ->gasPrice(UnitConverter::parseUnits($fixture['data']['gasPrice'], 'wei'))
        ->gasLimit(UnitConverter::parseUnits($fixture['data']['gasLimit'], 'wei'))
        ->nonce($fixture['data']['nonce'])
        ->validatorPassphrase(BLS_PASSPHRASE)
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
