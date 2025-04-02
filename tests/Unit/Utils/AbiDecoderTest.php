<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\Enums\ContractAbiType;
use ArkEcosystem\Crypto\Utils\AbiDecoder;

test('it should decode vote payload', function () {
    $decoder = new AbiDecoder();

    $functionName = 'vote';
    $args         = ['0x512F366D524157BcF734546eB29a6d687B762255'];
    $data         = '0x6dd7d8ea000000000000000000000000512f366d524157bcf734546eb29a6d687b762255';

    $decodedData = $decoder->decodeFunctionData($data);

    expect($decodedData)->toBe([
        'functionName' => $functionName,
        'args'         => $args,
    ]);
});

test('should decode multipayment payload', function () {
    $decoder = new AbiDecoder(ContractAbiType::MULTIPAYMENT);

    $functionName = 'pay';
    $args         = [
        ['0xb693449AdDa7EFc015D87944EAE8b7C37EB1690A', '0xb693449AdDa7EFc015D87944EAE8b7C37EB1690A'],
        ['100000000', '200000000'],
    ];

    $data = '084ce708000000000000000000000000000000000000000000000000000000000000004000000000000000000000000000000000000000000000000000000000000000a00000000000000000000000000000000000000000000000000000000000000002000000000000000000000000b693449adda7efc015d87944eae8b7c37eb1690a000000000000000000000000b693449adda7efc015d87944eae8b7c37eb1690a00000000000000000000000000000000000000000000000000000000000000020000000000000000000000000000000000000000000000000000000005f5e100000000000000000000000000000000000000000000000000000000000bebc200';

    $decodedData = $decoder->decodeFunctionData($data);

    expect($decodedData)->toBe([
        'functionName' => $functionName,
        'args'         => $args,
    ]);
});

test('it should decode function with abi', function () {
    $functionSignature = 'function name() view returns (string)';
    $payload           = '0x000000000000000000000000000000000000000000000000000000000000002000000000000000000000000000000000000000000000000000000000000000064441524b32300000000000000000000000000000000000000000000000000000';

    $decoded = AbiDecoder::decodeFunctionWithAbi($functionSignature, $payload);

    expect($decoded)->toBe(['DARK20']);
});
