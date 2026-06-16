<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\Enums\ContractAbiType;
use ArkEcosystem\Crypto\Utils\AbiDecoder;
use kornrunner\Keccak;

it('should decode vote payload', function () {
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

it('should fail to decode an unknown function', function () {
    $decoder = new AbiDecoder();

    $data = '0x1dd7d8ea000000000000000000000000512f366d524157bcf734546eb29a6d687b762255';

    $decoder->decodeFunctionData($data);
})->throws(Exception::class, 'Function selector not found in ABI: 1dd7d8ea');

it('should throw an exception if there is no data', function () {
    $decoder = new AbiDecoder();

    $data = '0x6dd7d8ea';

    $decoder->decodeFunctionData($data);
})->throws(Exception::class, 'No data to decode');

it('should decode function with abi', function () {
    $functionSignature = 'function name() view returns (string)';
    $payload           = '0x000000000000000000000000000000000000000000000000000000000000002000000000000000000000000000000000000000000000000000000000000000064441524b32300000000000000000000000000000000000000000000000000000';

    $decoded = AbiDecoder::decodeFunctionWithAbi($functionSignature, $payload);

    expect($decoded)->toBe(['DARK20']);
});

it('should handle functions with inputs', function () {
    $functionSignature = 'function name(uint256) view returns (string)';
    $payload           = '0x000000000000000000000000000000000000000000000000000000000000002000000000000000000000000000000000000000000000000000000000000000064441524b32300000000000000000000000000000000000000000000000000000';

    $decoded = AbiDecoder::decodeFunctionWithAbi($functionSignature, $payload);

    expect($decoded)->toBe(['DARK20']);
});

it('should throw an exception when decoding invalid function', function () {
    $functionSignature = 'function invalid view returns string';
    $payload           = '0x000000000000000000000000000000000000000000000000000000000000002000000000000000000000000000000000000000000000000000000000000000064441524b32300000000000000000000000000000000000000000000000000000';

    $decoded = AbiDecoder::decodeFunctionWithAbi($functionSignature, $payload);
})->throws(Exception::class, 'Invalid function signature: function invalid view returns string');

it('should decode an address', function () {
    $payload = '000000000000000000000000b693449adda7efc015d87944eae8b7c37eb1690a';

    $decoded = AbiDecoder::decodeAddress(hex2bin($payload), 0);

    expect($decoded)->toBe(['0xb693449AdDa7EFc015D87944EAE8b7C37EB1690A', 32]);
});

it('should decode a true boolean', function () {
    $payload = '0001';

    $decoded = AbiDecoder::decodeBool(hex2bin($payload), 0);

    expect($decoded)->toBe([true, 32]);
});

it('should decode a false boolean', function () {
    $payload = '0000';

    $decoded = AbiDecoder::decodeBool(hex2bin($payload), 0);

    expect($decoded)->toBe([false, 32]);
});

it('should decode an unsigned int', function () {
    $payload = '000000000000000000000000000000000000000000000000000000000bebc200';

    $decoded = AbiDecoder::decodeNumber(hex2bin($payload), 0, 256, false);

    expect($decoded)->toBe(['200000000', 32]);
});

it('should decode a signed int', function () {
    $payload = 'fffffffffffffffffffffffffffffffffffffffffffffffffffffffff4143E00';

    $decoded = AbiDecoder::decodeNumber(hex2bin($payload), 0, 256, true);

    expect($decoded)->toBe(['-200000000', 32]);
});

it('should decode a string', function () {
    // ABI-encoded string "test": offset(0x20), length(4), data("test")
    $payload = '0000000000000000000000000000000000000000000000000000000000000020000000000000000000000000000000000000000000000000000000000000000474657374';

    $decoded = AbiDecoder::decodeString(hex2bin($payload), 0);

    expect($decoded)->toBe(['test', 32]);
});

it('should decode dynamic bytes', function () {
    $payload = '0000000000000000000000000000000000000000000000000000000000000020000000000000000000000000000000000000000000000000000000000000000474657374';

    $decoded = AbiDecoder::decodeDynamicBytes(hex2bin($payload), 0);

    expect($decoded)->toBe(['0x74657374', 32]);
});

it('should decode dynamic bytes at a non-zero slot offset', function () {
    // Simulates the second `bytes` argument in a two-argument function.
    // The pointer stored at slot 1 (offset=32) is 64 — an absolute offset from
    // params start, NOT relative to the slot position. The old code added $offset
    // to the pointer value, landing at byte 96 (mid-data) instead of byte 64 (length word).
    $payload =
        str_repeat('00', 32).                                              // slot 0: unused
        '0000000000000000000000000000000000000000000000000000000000000040'. // slot 1: ptr = 64 (absolute)
        '0000000000000000000000000000000000000000000000000000000000000004'. // byte 64: length = 4
        '7465737400000000000000000000000000000000000000000000000000000000'; // byte 96: data "test" + pad

    $decoded = AbiDecoder::decodeDynamicBytes(hex2bin($payload), 32);

    expect($decoded)->toBe(['0x74657374', 32]);
});


it('should decode fixed bytes', function () {
    $payload = '74657374';

    $decoded = AbiDecoder::decodeFixedBytes(hex2bin($payload), 0, 4);

    expect($decoded)->toBe(['0x74657374', 32]);
});

it('should decode an array', function () {
    $payload = '000000000000000000000000b693449adda7efc015d87944eae8b7c37eb1690a000000000000000000000000b693449adda7efc015d87944eae8b7c37eb1690a';

    $param = [
        'name' => 'recipients',
        'type' => 'address',
    ];

    $decoded = AbiDecoder::decodeArray(hex2bin($payload), 0, $param, 2);

    expect($decoded)->toBe([
        ['0xb693449AdDa7EFc015D87944EAE8b7C37EB1690A', '0xb693449AdDa7EFc015D87944EAE8b7C37EB1690A'],
        32,
    ]);
});

it('should decode a tuple', function () {
    $payload = '000000000000000000000000b693449adda7efc015d87944eae8b7c37eb1690a000000000000000000000000b693449adda7efc015d87944eae8b7c37eb1690a';

    $param = [
        'name'         => 'recipients',
        'type'         => 'address',
        'components'   => [
            [
                'name' => 'from',
                'type' => 'address',
            ],
            [
                'name' => 'to',
                'type' => 'address',
            ],
        ],
    ];

    $decoded = AbiDecoder::decodeTuple(hex2bin($payload), 0, $param);

    expect($decoded)->toBe([
        [
            'from' => '0xb693449AdDa7EFc015D87944EAE8b7C37EB1690A',
            'to'   => '0xb693449AdDa7EFc015D87944EAE8b7C37EB1690A',
        ],
        32,
    ]);
});

it('should read an unsigned int', function () {
    $payload = '000000000000000000000000000000000000000000000000000000000bebc200';

    $decoded = AbiDecoder::readUInt(hex2bin($payload), 0, 4);

    expect($decoded)->toBe(200000000);
});

it('should handle boolean param types', function () {
    $object    = new AbiDecoder();
    $reflector = new ReflectionObject($object);
    $method    = $reflector->getMethod('decodeParameter');
    $method->setAccessible(true);

    $param = [
        'name' => 'isActive',
        'type' => 'bool',
    ];

    $result = $method->invokeArgs(null, [
        hex2bin('0001'),
        0,
        $param,
    ]);

    expect($result)->toBe([true, 32]);

    $result = $method->invokeArgs(null, [
        hex2bin('0000'),
        0,
        $param,
    ]);

    expect($result)->toBe([false, 32]);
});

it('should handle bytes param types', function () {
    $object    = new AbiDecoder();
    $reflector = new ReflectionObject($object);
    $method    = $reflector->getMethod('decodeParameter');
    $method->setAccessible(true);

    $param = [
        'name' => 'text',
        'type' => 'bytes',
    ];

    $result = $method->invokeArgs(null, [
        hex2bin('0000000000000000000000000000000000000000000000000000000000000020000000000000000000000000000000000000000000000000000000000000000474657374'),
        0,
        $param,
    ]);

    expect($result)->toBe(['0x74657374', 32]);
});

it('should handle dynamic byte length param types', function () {
    $object    = new AbiDecoder();
    $reflector = new ReflectionObject($object);
    $method    = $reflector->getMethod('decodeParameter');
    $method->setAccessible(true);

    $param = [
        'name' => 'text',
        'type' => 'bytes4',
    ];

    $result = $method->invokeArgs(null, [
        hex2bin('74657374'),
        0,
        $param,
    ]);

    expect($result)->toBe(['0x74657374', 32]);
});

it('should handle signed integer param types', function () {
    $object    = new AbiDecoder();
    $reflector = new ReflectionObject($object);
    $method    = $reflector->getMethod('decodeParameter');
    $method->setAccessible(true);

    $param = [
        'name' => 'text',
        'type' => 'int256',
    ];

    $result = $method->invokeArgs(null, [
        hex2bin('fffffffffffffffffffffffffffffffffffffffffffffffffffffffff4143E00'),
        0,
        $param,
    ]);

    expect($result)->toBe(['-200000000', 32]);
});

it('should handle unsigned integer param types', function () {
    $object    = new AbiDecoder();
    $reflector = new ReflectionObject($object);
    $method    = $reflector->getMethod('decodeParameter');
    $method->setAccessible(true);

    $param = [
        'name' => 'text',
        'type' => 'uint256',
    ];

    $result = $method->invokeArgs(null, [
        hex2bin('000000000000000000000000000000000000000000000000000000000bebc200'),
        0,
        $param,
    ]);

    expect($result)->toBe(['200000000', 32]);
});

it('should handle tuple param types', function () {
    $object    = new AbiDecoder();
    $reflector = new ReflectionObject($object);
    $method    = $reflector->getMethod('decodeParameter');
    $method->setAccessible(true);

    $param = [
        'name'         => 'text',
        'type'         => 'tuple',
        'components'   => [
            [
                'name' => 'from',
                'type' => 'address',
            ],
            [
                'name' => 'to',
                'type' => 'address',
            ],
        ],
    ];

    $result = $method->invokeArgs(null, [
        hex2bin('000000000000000000000000b693449adda7efc015d87944eae8b7c37eb1690a000000000000000000000000b693449adda7efc015d87944eae8b7c37eb1690a'),
        0,
        $param,
    ]);

    expect($result)->toBe([
        [
            'from' => '0xb693449AdDa7EFc015D87944EAE8b7C37EB1690A',
            'to'   => '0xb693449AdDa7EFc015D87944EAE8b7C37EB1690A',
        ],
        32,
    ]);
});

it('should throw an exception for invalid param types', function () {
    $object    = new AbiDecoder();
    $reflector = new ReflectionObject($object);
    $method    = $reflector->getMethod('decodeParameter');
    $method->setAccessible(true);

    $param = [
        'name' => 'text',
        'type' => 'testing',
    ];

    $method->invokeArgs(null, [
        hex2bin('000000000000000000000000000000000000000000000000000000000bebc200'),
        0,
        $param,
    ]);
})->throws(Exception::class, 'Unsupported type: testing');

test('should decode error payload', function () {
    $decoder = new AbiDecoder();

    $decodedData = $decoder->decodeError('cd03235e');

    expect($decodedData)->toBe('CallerIsNotValidator');
});

test('should throw exception if error payload does not exist', function () {
    $decoder = new AbiDecoder();

    $decoder->decodeError('123456');
})->throws(Exception::class, 'Function selector not found in ABI: 123456');

test('should precompute selector maps for custom abi items', function () {
    $decoder = new AbiDecoder(ContractAbiType::CUSTOM, dirname(__DIR__, 2).'/fixtures/mock-abi-selectors.json');

    $reflector = new ReflectionObject($decoder);
    $functions = $reflector->getProperty('functionSelectorMap');
    $errors    = $reflector->getProperty('errorSelectorMap');
    $functions->setAccessible(true);
    $errors->setAccessible(true);

    $functionSelector = substr(Keccak::hash('transfer(address,uint256)', 256), 0, 8);
    $errorSelector    = substr(Keccak::hash('InsufficientBalance(uint256,uint256)', 256), 0, 8);

    $functionMap = $functions->getValue($decoder);
    $errorMap    = $errors->getValue($decoder);

    expect($functionMap)->toHaveKey($functionSelector);
    expect($functionMap[$functionSelector]['name'])->toBe('transfer');
    expect($errorMap)->toHaveKey($errorSelector);
    expect($errorMap[$errorSelector]['name'])->toBe('InsufficientBalance');
});

test('should decode function and error payloads using custom selector maps', function () {
    $decoder = new AbiDecoder(ContractAbiType::CUSTOM, dirname(__DIR__, 2).'/fixtures/mock-abi-selectors.json');

    $functionSelector = substr(Keccak::hash('transfer(address,uint256)', 256), 0, 8);
    $errorSelector    = substr(Keccak::hash('InsufficientBalance(uint256,uint256)', 256), 0, 8);

    $to       = 'b693449adda7efc015d87944eae8b7c37eb1690a';
    $amount   = str_pad(dechex(7), 64, '0', STR_PAD_LEFT);
    $data     = '0x'.$functionSelector.str_pad($to, 64, '0', STR_PAD_LEFT).$amount;
    $decoded  = $decoder->decodeFunctionData($data);
    $abiError = $decoder->decodeError('0x'.$errorSelector);

    expect($decoded)->toBe([
        'functionName' => 'transfer',
        'args'         => ['0xb693449AdDa7EFc015D87944EAE8b7C37EB1690A', '7'],
    ]);
    expect($abiError)->toBe('InsufficientBalance');
});
