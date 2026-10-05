<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\Identities\Address;
use ArkEcosystem\Crypto\Transactions\Builder\ValidatorUpdateBuilder;
use ArkEcosystem\Crypto\Utils\UnitConverter;

it('should sign it with a passphrase', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'validator-update');

    $builder = ValidatorUpdateBuilder::new()
        ->gasPrice(UnitConverter::parseUnits($fixture['data']['gasPrice'], 'wei'))
        ->gasLimit(UnitConverter::parseUnits($fixture['data']['gasLimit'], 'wei'))
        ->nonce($fixture['data']['nonce'])
        ->validatorProof($this->secondPassphrase, $fixture['data']['from'])
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
    $fixture = $this->getTransactionFixture('evm_call', 'validator-update');

    $builder = ValidatorUpdateBuilder::new()
        ->gasPrice(UnitConverter::parseUnits($fixture['data']['gasPrice'], 'wei'))
        ->gasLimit(UnitConverter::parseUnits($fixture['data']['gasLimit'], 'wei'))
        ->nonce($fixture['data']['nonce'])
        ->validatorProof($this->secondPassphrase, $fixture['data']['from'])
        ->sign($this->passphrase);

    expect((string) $builder)->toBe($builder->toJson());
});

it('should derive correct validatorPublicKey and validatorProof from passphrase', function () {
    $builder = ValidatorUpdateBuilder::new()
        ->validatorProof($this->secondPassphrase, Address::fromPassphrase($this->passphrase));

    expect($builder->transaction->data['validatorPublicKey'])
        ->toBe('a18dba7811b212bbb2f080d7c69935998ffbe7b38586e2d3e9e12079ea789996d1c69feb158c002aed327f69865be496');

    expect($builder->transaction->data['validatorProof'])
        ->toBe('8dff1b303bfacacb2eb716892b79123b256314d8fc91635aae307f0fd7faf58019bba25c494b38eb1adccd91f49923ef00e39947932d7edffe710c48b9ab61ad4a9bd48f41f9babcfdd5c88bf3ad74c4d70d091a407310297b1dc4bc84ec6d5f');
});

it('should not have a value method', function () {
    expect(method_exists(ValidatorUpdateBuilder::class, 'value'))->toBeFalse();
});

it('should reject an invalid registrant address', function () {
    ValidatorUpdateBuilder::new()->validatorProof($this->secondPassphrase, '0x1234');
})->throws(InvalidArgumentException::class, 'Invalid registrant address');

it('should convert to an array', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'validator-update');

    $builder = ValidatorUpdateBuilder::new()
        ->gasPrice(UnitConverter::parseUnits($fixture['data']['gasPrice'], 'wei'))
        ->gasLimit(UnitConverter::parseUnits($fixture['data']['gasLimit'], 'wei'))
        ->nonce($fixture['data']['nonce'])
        ->validatorProof($this->secondPassphrase, $fixture['data']['from'])
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
