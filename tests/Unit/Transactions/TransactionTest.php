<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\Identities\PrivateKey;

/*
 * @covers \ArkEcosystem\Crypto\Transactions\Types\Transaction
 */
it('should compute the id of the transaction', function () {
    $actual = $this->getTransaction()->getId();

    expect(strlen($actual))->toBe(64);
});

it('should sign the transaction using a passphrase', function () {
    $privateKey = PrivateKey::fromPassphrase('this is a top secret passphrase');

    $transaction                    = $this->getTransaction();
    $transaction->data['signature'] = null;

    expect($transaction->data['signature'])->toBeEmpty();

    $transaction->sign($privateKey);

    expect($transaction->data['r'])->not->toBeEmpty();
    expect($transaction->data['s'])->not->toBeEmpty();
    expect($transaction->data['v'])->not->toBeEmpty();
});

it('should verify the transaction', function () {
    $actual = $this->getTransaction()->verify();

    expect($actual)->toBeTrue();
});

it('should turn the transaction to an array', function () {
    $actual = $this->getTransaction()->toArray();

    expect($actual)->toBeArray();
});

it('should turn the transaction to json', function () {
    $actual = $this->getTransaction()->toJson();

    expect($actual)->toBeString();
});
