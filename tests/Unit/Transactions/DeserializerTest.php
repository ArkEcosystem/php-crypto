<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\Transactions\Types\EvmCall;
use ArkEcosystem\Crypto\Transactions\Types\Multipayment;
use ArkEcosystem\Crypto\Transactions\Types\Transfer;
use ArkEcosystem\Crypto\Transactions\Types\Unvote;
use ArkEcosystem\Crypto\Transactions\Types\UsernameRegistration;
use ArkEcosystem\Crypto\Transactions\Types\UsernameResignation;
use ArkEcosystem\Crypto\Transactions\Types\ValidatorRegistration;
use ArkEcosystem\Crypto\Transactions\Types\ValidatorResignation;
use ArkEcosystem\Crypto\Transactions\Types\Vote;

/*
 * @covers \ArkEcosystem\Crypto\Transactions\Deserializer
 */
it('should deserialize a transfer signed with a passphrase', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'transfer');

    $transaction = $this->assertTransaction($fixture);

    expect($transaction)->toBeInstanceOf(Transfer::class);
});

it('should deserialize a transfer signed with a passphrase with 0 value', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'transfer-0');

    $transaction = $this->assertTransaction($fixture);

    expect($transaction)->toBeInstanceOf(EvmCall::class);
});

it('should deserialize a vote signed with a passphrase', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'vote');

    $transaction = $this->assertTransaction($fixture);

    expect($transaction->data['vote'])->toEqual('0xC3bBE9B1CeE1ff85Ad72b87414B0E9B7F2366763');
    expect($transaction->data['id'])->toEqual($fixture['data']['id']);
    expect($transaction)->toBeInstanceOf(Vote::class);
});

it('should deserialize a unvote signed with a passphrase', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'unvote');

    $transaction = $this->assertTransaction($fixture);

    expect($transaction)->toBeInstanceOf(Unvote::class);
});

it('should deserialize a validator registration signed with a passphrase', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'validator-registration');

    $transaction = $this->assertTransaction($fixture);

    expect($transaction)->toBeInstanceOf(ValidatorRegistration::class);
});

it('should deserialize a validator resignation signed with a passphrase', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'validator-resignation');

    $transaction = $this->assertTransaction($fixture);

    expect($transaction)->toBeInstanceOf(ValidatorResignation::class);
});

it('should deserialize a username registration signed with a passphrase', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'username-registration');

    $transaction = $this->assertTransaction($fixture);

    expect($transaction)->toBeInstanceOf(UsernameRegistration::class);
});

it('should deserialize a username resignation signed with a passphrase', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'username-resignation');

    $transaction = $this->assertTransaction($fixture);

    expect($transaction)->toBeInstanceOf(UsernameResignation::class);
});

it('should deserialize a multipayment signed with a passphrase', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'multipayment');

    $transaction = $this->assertTransaction($fixture);

    expect($transaction)->toBeInstanceOf(Multipayment::class);
});
