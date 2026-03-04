<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\Utils\TransactionEncoder;

it('should encode a multipayment payload', function () {
    $encoded = TransactionEncoder::multiPayment(
        ['0xb693449AdDa7EFc015D87944EAE8b7C37EB1690A'],
        ['1000']
    );

    expect($encoded)->toBe(
        '0x084ce7080000000000000000000000000000000000000000000000000000000000000040'
        .'0000000000000000000000000000000000000000000000000000000000000080'
        .'0000000000000000000000000000000000000000000000000000000000000001'
        .'000000000000000000000000b693449adda7efc015d87944eae8b7c37eb1690a'
        .'0000000000000000000000000000000000000000000000000000000000000001'
        .'00000000000000000000000000000000000000000000000000000000000003e8'
    );
});

it('should encode a token transfer payload', function () {
    $encoded = TransactionEncoder::tokenTransfer(
        '0xA5cc0BfEB09742C5e4C610f2EBaaB82Eb142Ca10',
        '1000000000000'
    );

    expect($encoded)->toBe(
        '0xa9059cbb000000000000000000000000a5cc0bfeb09742c5e4c610f2ebaab82eb142ca10'
        .'000000000000000000000000000000000000000000000000000000e8d4a51000'
    );
});

it('should reject invalid address format in token transfer', function () {
    TransactionEncoder::tokenTransfer('', '1000');
})->throws(Exception::class);

it('should reject invalid address format in multipayment', function () {
    TransactionEncoder::multiPayment(['0x123'], ['1000']);
})->throws(Exception::class);

it('should encode validator payloads', function () {
    $validatorPublicKey = '30954f46d6097a1d314e900e66e11e0dad0a57cd03e04ec99f0dedd1c765dcb11e6d7fa02e22cf40f9ee23d9cc1c0624';

    expect(TransactionEncoder::validatorRegistration($validatorPublicKey))->toBe(
        '0x602a9eee0000000000000000000000000000000000000000000000000000000000000020'
        .'0000000000000000000000000000000000000000000000000000000000000030'
        .'30954f46d6097a1d314e900e66e11e0dad0a57cd03e04ec99f0dedd1c765dcb1'
        .'1e6d7fa02e22cf40f9ee23d9cc1c062400000000000000000000000000000000'
    );
    expect(TransactionEncoder::validatorResignation())->toBe('0xb85f5da2');
});

it('should encode validator registration with a prefixed public key', function () {
    $validatorPublicKey = '0x30954f46d6097a1d314e900e66e11e0dad0a57cd03e04ec99f0dedd1c765dcb11e6d7fa02e22cf40f9ee23d9cc1c0624';

    expect(TransactionEncoder::validatorRegistration($validatorPublicKey))->toBe(
        '0x602a9eee0000000000000000000000000000000000000000000000000000000000000020'
        .'0000000000000000000000000000000000000000000000000000000000000030'
        .'30954f46d6097a1d314e900e66e11e0dad0a57cd03e04ec99f0dedd1c765dcb1'
        .'1e6d7fa02e22cf40f9ee23d9cc1c062400000000000000000000000000000000'
    );
});

it('should encode username payloads', function () {
    expect(TransactionEncoder::usernameRegistration('fixture'))->toBe(
        '0x36a941340000000000000000000000000000000000000000000000000000000000000020'
        .'0000000000000000000000000000000000000000000000000000000000000007'
        .'6669787475726500000000000000000000000000000000000000000000000000'
    );
    expect(TransactionEncoder::usernameResignation())->toBe('0xebed6dab');
});

it('should encode consensus vote payloads', function () {
    expect(TransactionEncoder::vote('0x512F366D524157BcF734546eB29a6d687B762255'))->toBe(
        '0x6dd7d8ea000000000000000000000000512f366d524157bcf734546eb29a6d687b762255'
    );
    expect(TransactionEncoder::unvote())->toBe('0x3174b689');
});
