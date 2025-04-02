<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\Exceptions\InvalidUsernameException;
use ArkEcosystem\Crypto\Transactions\Builder\UsernameRegistrationBuilder;
use ArkEcosystem\Crypto\Utils\UnitConverter;

it('should sign it with a passphrase', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'username-registration');

    $builder = UsernameRegistrationBuilder::new()
        ->gasPrice(UnitConverter::parseUnits($fixture['data']['gasPrice'], 'wei'))
        ->nonce($fixture['data']['nonce'])
        ->network($fixture['data']['network'])
        ->gasLimit(UnitConverter::parseUnits($fixture['data']['gasLimit'], 'wei'))
        ->username('fixture')
        ->sign($this->passphrase);

    expect((string) $builder->transaction->data['gasPrice'])->toBe((string) $fixture['data']['gasPrice']);
    expect($builder->transaction->data['nonce'])->toBe($fixture['data']['nonce']);
    expect($builder->transaction->data['network'])->toBe($fixture['data']['network']);
    expect((string) $builder->transaction->data['gasLimit'])->toBe((string) $fixture['data']['gasLimit']);
    expect($builder->transaction->data['v'])->toBe($fixture['data']['v']);
    expect($builder->transaction->data['r'])->toBe($fixture['data']['r']);
    expect($builder->transaction->data['s'])->toBe($fixture['data']['s']);

    expect($builder->transaction->serialize()->getHex())->toBe($fixture['serialized']);

    expect($builder->transaction->data['id'])->toBe($fixture['data']['id']);

    expect($builder->verify())->toBeTrue();
});

it('should throw exception for invalid username', function () {
    $action = function () {
        $fixture = $this->getTransactionFixture('evm_call', 'username-registration');

        UsernameRegistrationBuilder::new()
            ->gasPrice(UnitConverter::parseUnits($fixture['data']['gasPrice'], 'wei'))
            ->nonce($fixture['data']['nonce'])
            ->network($fixture['data']['network'])
            ->gasLimit(UnitConverter::parseUnits($fixture['data']['gasLimit'], 'wei'))
            ->username('this_is_a_very_long_username_that_is_invalid')
            ->sign($this->passphrase);
    };

    expect($action)->toThrow(
        InvalidUsernameException::class,
        'Username must be between 1 and 20 characters long'
    );
});
