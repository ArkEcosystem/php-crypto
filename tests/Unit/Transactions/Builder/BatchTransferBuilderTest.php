<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\Enums\ContractAddresses;
use ArkEcosystem\Crypto\Transactions\Builder\BatchTransferBuilder;
use ArkEcosystem\Crypto\Utils\UnitConverter;
use Brick\Math\BigDecimal;

beforeEach(function () {
    $this->fixture = $this->getFixture('transactions/batch-transfer');
});

it('should build a batch transfer', function () {
    $builder = BatchTransferBuilder::new()
        ->nonce($this->fixture['data']['nonce'])
        ->gasPrice(UnitConverter::parseUnits($this->fixture['data']['gasPrice'], 'wei'))
        ->gasLimit(UnitConverter::parseUnits($this->fixture['data']['gasLimit'], 'wei'))
        ->addRecipient('0x6F0182a0cc707b055322CcF6d4CB6a5Aff1aEb22', BigDecimal::of('100000'))
        ->addRecipient('0xc3bbe9b1cee1ff85ad72b87414b0e9b7f2366763', BigDecimal::of('200000'))
        ->tokenAddress($this->fixture['data']['tokenAddress'])
        ->sign($this->passphrase);

    expect((string) $builder->transaction->data['gasPrice'])->toBe($this->fixture['data']['gasPrice']);
    expect((string) $builder->transaction->data['gasLimit'])->toBe($this->fixture['data']['gasLimit']);
    expect($builder->transaction->data['nonce'])->toBe($this->fixture['data']['nonce']);
    expect($builder->transaction->data['value']->isZero())->toBeTrue();
    expect($builder->transaction->data['to'])->toBe(ContractAddresses::BATCH_TRANSFER->value);
    expect($builder->transaction->data['data'])->toBe($this->fixture['data']['data']);
    expect($builder->transaction->data['v'])->toBe($this->fixture['data']['v']);
    expect($builder->transaction->data['r'])->toBe($this->fixture['data']['r']);
    expect($builder->transaction->data['s'])->toBe($this->fixture['data']['s']);
    expect($builder->transaction->data['hash'])->toBe($this->fixture['data']['hash']);

    expect($builder->transaction->serialize()->getHex())->toBe($this->fixture['serialized']);
    expect($builder->verify())->toBeTrue();
});

it('should encode a single recipient', function () {
    $builder = BatchTransferBuilder::new()
        ->nonce($this->fixture['data']['nonce'])
        ->gasPrice(UnitConverter::parseUnits($this->fixture['data']['gasPrice'], 'wei'))
        ->gasLimit(UnitConverter::parseUnits($this->fixture['data']['gasLimit'], 'wei'))
        ->addRecipient('0x6F0182a0cc707b055322CcF6d4CB6a5Aff1aEb22', BigDecimal::of('100000'))
        ->tokenAddress($this->fixture['data']['tokenAddress'])
        ->sign($this->passphrase);

    expect($builder->verify())->toBeTrue();
    expect($builder->transaction->data['value']->isZero())->toBeTrue();
    expect($builder->transaction->data['data'])->toStartWith('4885b254');
});

it('should encode large amounts', function () {
    $builder = BatchTransferBuilder::new()
        ->nonce($this->fixture['data']['nonce'])
        ->gasPrice(UnitConverter::parseUnits($this->fixture['data']['gasPrice'], 'wei'))
        ->gasLimit(UnitConverter::parseUnits($this->fixture['data']['gasLimit'], 'wei'))
        ->addRecipient('0x6F0182a0cc707b055322CcF6d4CB6a5Aff1aEb22', BigDecimal::of('1000000000000000000000'))
        ->tokenAddress($this->fixture['data']['tokenAddress'])
        ->sign($this->passphrase);

    expect($builder->verify())->toBeTrue();
});

it('should convert to json when casting to string', function () {
    $builder = BatchTransferBuilder::new()
        ->nonce($this->fixture['data']['nonce'])
        ->gasPrice(UnitConverter::parseUnits($this->fixture['data']['gasPrice'], 'wei'))
        ->gasLimit(UnitConverter::parseUnits($this->fixture['data']['gasLimit'], 'wei'))
        ->addRecipient('0x6F0182a0cc707b055322CcF6d4CB6a5Aff1aEb22', BigDecimal::of('100000'))
        ->tokenAddress($this->fixture['data']['tokenAddress'])
        ->sign($this->passphrase);

    expect((string) $builder)->toBe($builder->toJson());
});

it('should convert to an array', function () {
    $builder = BatchTransferBuilder::new()
        ->nonce($this->fixture['data']['nonce'])
        ->gasPrice(UnitConverter::parseUnits($this->fixture['data']['gasPrice'], 'wei'))
        ->gasLimit(UnitConverter::parseUnits($this->fixture['data']['gasLimit'], 'wei'))
        ->addRecipient('0x6F0182a0cc707b055322CcF6d4CB6a5Aff1aEb22', BigDecimal::of('100000'))
        ->addRecipient('0xc3bbe9b1cee1ff85ad72b87414b0e9b7f2366763', BigDecimal::of('200000'))
        ->tokenAddress($this->fixture['data']['tokenAddress'])
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

it('should throw when signing with no recipients', function () {
    BatchTransferBuilder::new()
        ->gasPrice(UnitConverter::parseUnits($this->fixture['data']['gasPrice'], 'wei'))
        ->gasLimit(UnitConverter::parseUnits($this->fixture['data']['gasLimit'], 'wei'))
        ->tokenAddress($this->fixture['data']['tokenAddress'])
        ->sign($this->passphrase);
})->throws(Exception::class, 'Must add at least one recipient before encoding.');

it('should throw when signing without a token address', function () {
    BatchTransferBuilder::new()
        ->gasPrice(UnitConverter::parseUnits($this->fixture['data']['gasPrice'], 'wei'))
        ->gasLimit(UnitConverter::parseUnits($this->fixture['data']['gasLimit'], 'wei'))
        ->addRecipient('0x6F0182a0cc707b055322CcF6d4CB6a5Aff1aEb22', BigDecimal::of('100000'))
        ->sign($this->passphrase);
})->throws(Exception::class, 'Must set tokenAddress before encoding.');
