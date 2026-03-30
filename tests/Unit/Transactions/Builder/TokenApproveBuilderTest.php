<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\Transactions\Builder\TokenApproveBuilder;
use ArkEcosystem\Crypto\Utils\UnitConverter;
use Brick\Math\BigDecimal;

it('should build a token approve payload', function () {
    $contractAddress = '0x6F0182a0cc707b055322CcF6d4CB6a5Aff1aEb22';
    $spender         = '0xA5cc0BfEB09742C5e4C610f2EBaaB82Eb142Ca10';
    $amount          = BigDecimal::of('1000000000000');
    $expectedPayload = '095ea7b3000000000000000000000000a5cc0bfeb09742c5e4c610f2ebaab82eb142ca10000000000000000000000000000000000000000000000000000000e8d4a51000';

    $builder = TokenApproveBuilder::new()
        ->contractAddress($contractAddress)
        ->spender($spender, $amount);

    expect($builder->transaction->data['to'])->toBe($contractAddress);
    expect($builder->transaction->data['value']->isZero())->toBeTrue();
    expect($builder->transaction->data['data'])->toBe($expectedPayload);
});

it('should sign and verify a token approve', function () {
    $builder = TokenApproveBuilder::new()
        ->contractAddress('0x6F0182a0cc707b055322CcF6d4CB6a5Aff1aEb22')
        ->spender('0xA5cc0BfEB09742C5e4C610f2EBaaB82Eb142Ca10', BigDecimal::of('1000000000000'))
        ->gasPrice(UnitConverter::parseUnits('5000000000', 'wei'))
        ->gasLimit(UnitConverter::parseUnits('21000', 'wei'))
        ->nonce('1')
        ->sign($this->passphrase);

    expect($builder->verify())->toBeTrue();
    expect($builder->transaction->data['data'])->toStartWith('095ea7b3');
    expect($builder->transaction->data['to'])->toBe('0x6F0182a0cc707b055322CcF6d4CB6a5Aff1aEb22');
    expect($builder->transaction->data['value']->isZero())->toBeTrue();
});
