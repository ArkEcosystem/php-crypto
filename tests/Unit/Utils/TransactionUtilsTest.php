<?php

use ArkEcosystem\Crypto\Binary\Buffer\Reader\Buffer;
use ArkEcosystem\Crypto\Utils\RlpDecoder;
use ArkEcosystem\Crypto\Utils\TransactionUtils;
use Brick\Math\BigDecimal;
use Brick\Math\BigNumber;

it('should convert a transaction to a buffer', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'username-resignation');

    $transaction = TransactionUtils::toBuffer($fixture['data']);

    expect($transaction->getHex())->toBe($fixture['serialized']);
});

it('should convert a transaction to a buffer when data starts with 0x', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'username-resignation');

    $fixture['data']['data'] = '0x' . $fixture['data']['data'];

    $transaction = TransactionUtils::toBuffer($fixture['data']);

    expect($transaction->getHex())->toBe($fixture['serialized']);
});

it('should get the hash for a transaction', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'username-resignation');

    $transaction = TransactionUtils::toHash($fixture['data']);

    expect($transaction->getHex())->toBe($fixture['data']['hash']);
});

it('should handle string data starting with 0x', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'username-resignation');

    $fixture['data']['gasPrice'] = '0x'.dechex($fixture['data']['gasPrice']);

    $transaction = TransactionUtils::toBuffer($fixture['data']);

    expect($transaction->getHex())->toBe($fixture['serialized']);
});

it('should handle BigDecimal value', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'username-resignation');

    $fixture['data']['gasPrice'] = BigDecimal::of($fixture['data']['gasPrice']);

    $transaction = TransactionUtils::toBuffer($fixture['data']);

    expect($transaction->getHex())->toBe($fixture['serialized']);
});

it('should handle zero BigDecimal value', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'username-resignation');

    $fixture['data']['gasPrice'] = BigDecimal::zero();

    $transaction = TransactionUtils::toBuffer($fixture['data'], true);

    $decoded = RlpDecoder::decode('0x'.substr($transaction->getHex(), 2));

    expect($decoded[3])->toBe('0x');
});

it('should handle unknown value value', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'username-resignation');

    $fixture['data']['gasPrice'] = 123.456;

    $transaction = TransactionUtils::toBuffer($fixture['data'], true);

    $decoded = RlpDecoder::decode('0x'.substr($transaction->getHex(), 2));

    expect($decoded[3])->toBe('0x');
});

// toBuffer
// toHash
