<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\Transactions\Types\Transfer;
use ArkEcosystem\Crypto\Transactions\Types\Unvote;
use ArkEcosystem\Crypto\Transactions\Types\ValidatorRegistration;
use ArkEcosystem\Crypto\Transactions\Types\ValidatorResignation;
use ArkEcosystem\Crypto\Transactions\Types\Vote;

/*
 * @covers \ArkEcosystem\Crypto\Transactions\Serializer
 */
it('should serialize a transfer transaction', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'transfer');

    $transaction = new Transfer($fixture['data']);

    expect($transaction->serialize()->getHex())->toBe($fixture['serialized']);
});

it('should serialize a vote transaction', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'vote');

    $transaction = new Vote($fixture['data']);

    expect($transaction->serialize()->getHex())->toBe($fixture['serialized']);
});

it('should serialize a unvote transaction', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'unvote');

    $transaction = new Unvote($fixture['data']);

    expect($transaction->serialize()->getHex())->toBe($fixture['serialized']);
});

it('should serialize a validator registration transaction', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'validator-registration');

    $transaction = new ValidatorRegistration($fixture['data']);

    expect($transaction->serialize()->getHex())->toBe($fixture['serialized']);
});

it('should serialize a validator resignation transaction', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'validator-resignation');

    $transaction = new ValidatorResignation($fixture['data']);

    expect($transaction->serialize()->getHex())->toBe($fixture['serialized']);
});
