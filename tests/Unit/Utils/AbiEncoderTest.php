<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\Enums\ContractAbiType;
use ArkEcosystem\Crypto\Utils\AbiEncoder;
use Brick\Math\BigDecimal;

function testPrivateMethod(string $methodName, &$object): ReflectionMethod
{
    $object    = new AbiEncoder();
    $reflector = new ReflectionObject($object);
    $method    = $reflector->getMethod($methodName);
    $method->setAccessible(true);

    return $method;
}

beforeEach(function () {
    $this->encoder = new AbiEncoder();
});

it('should encode vote function call', function () {
    $functionName        = 'vote';
    $args                = ['0x512F366D524157BcF734546eB29a6d687B762255'];
    $expectedEncodedData = '0x6dd7d8ea000000000000000000000000512f366d524157bcf734546eb29a6d687b762255';

    $encodedData = $this->encoder->encodeFunctionCall($functionName, $args);

    expect($encodedData)->toBe($expectedEncodedData);
});

it('should encode getAllValidators function call', function () {
    $functionName        = 'getAllValidators';
    $args                = [];
    $expectedEncodedData = '0xf3513a37';

    $encodedData = $this->encoder->encodeFunctionCall($functionName, $args);

    expect($encodedData)->toBe($expectedEncodedData);
});

it('should handle function name as hex', function () {
    $encoder = new AbiEncoder(ContractAbiType::CUSTOM, dirname(dirname(__DIR__)).'/fixtures/mock-abi.json');

    $functionName = '0x1234';
    $args         = ['1'];

    $encodedData = $encoder->encodeFunctionCall($functionName, $args);

    expect($encodedData)->toBe('0x12340000000000000000000000000000000000000000000000000000000000000001');
});

it('should error for unknown function name', function () {
    $encoder = new AbiEncoder();

    $functionName = 'testFunction';
    $args         = ['0x512F366D524157BcF734546eB29a6d687B762255'];

    $encoder->encodeFunctionCall($functionName, $args);
})->throws(Exception::class, 'Function not found in ABI: testFunction');

it('should error for wrong amount of arguments in function call', function () {
    $encoder = new AbiEncoder(ContractAbiType::CUSTOM, dirname(dirname(__DIR__)).'/fixtures/mock-abi.json');

    $functionName = '0x1234';
    $args         = [];

    $encoder->encodeFunctionCall($functionName, $args);
})->throws(Exception::class, 'Length of parameters and values do not match');

it('should encode vote payload', function () {
    $encoder = new AbiEncoder();

    $functionName = 'vote';
    $args         = ['0x512F366D524157BcF734546eB29a6d687B762255'];
    $expectedData = '0x6dd7d8ea000000000000000000000000512f366d524157bcf734546eb29a6d687b762255';

    $encodedData = $encoder->encodeFunctionCall($functionName, $args);

    expect($encodedData)->toBe($expectedData);
});

test('should encode multipayment payload', function () {
    $encoder = new AbiEncoder(ContractAbiType::MULTIPAYMENT);

    $functionName = 'pay';
    $args         = [
        ['0xb693449AdDa7EFc015D87944EAE8b7C37EB1690A', '0xb693449AdDa7EFc015D87944EAE8b7C37EB1690A'],
        ['100000000', '200000000'],
    ];

    $expectedData = '0x084ce708000000000000000000000000000000000000000000000000000000000000004000000000000000000000000000000000000000000000000000000000000000a00000000000000000000000000000000000000000000000000000000000000002000000000000000000000000b693449adda7efc015d87944eae8b7c37eb1690a000000000000000000000000b693449adda7efc015d87944eae8b7c37eb1690a00000000000000000000000000000000000000000000000000000000000000020000000000000000000000000000000000000000000000000000000005f5e100000000000000000000000000000000000000000000000000000000000bebc200';

    $encodedData = $encoder->encodeFunctionCall($functionName, $args);

    expect($encodedData)->toBe($expectedData);
});

test('should encode batch transfer payload', function () {
    $encoder = new AbiEncoder(ContractAbiType::ERC20BATCH_TRANSFER);

    $functionName = 'batchTransferFrom';
    $args         = [
         '0x8444ab9d74212f28e14b089b62ed4a4a7a8fefb3',
        ['0xa5cc0bfeb09742c5e4c610f2ebaab82eb142ca10', '0xe3c31e486cca6eb2093c0f4883df949d45b021c5', '0xa5cc0bfeb09742c5e4c610f2ebaab82eb142ca10'],
        ['1', '2', '3'],
    ];

    $expectedData = '0x4885b2540000000000000000000000008444ab9d74212f28e14b089b62ed4a4a7a8fefb3000000000000000000000000000000000000000000000000000000000000006000000000000000000000000000000000000000000000000000000000000000e00000000000000000000000000000000000000000000000000000000000000003000000000000000000000000a5cc0bfeb09742c5e4c610f2ebaab82eb142ca10000000000000000000000000e3c31e486cca6eb2093c0f4883df949d45b021c5000000000000000000000000a5cc0bfeb09742c5e4c610f2ebaab82eb142ca100000000000000000000000000000000000000000000000000000000000000003000000000000000000000000000000000000000000000000000000000000000100000000000000000000000000000000000000000000000000000000000000020000000000000000000000000000000000000000000000000000000000000003';

    $encodedData = $encoder->encodeFunctionCall($functionName, $args);

    expect($encodedData)->toBe($expectedData);
});

it('should throw an exception for unknown function', function () {
    $encoder = new AbiEncoder();

    $functionName = 'unknownFunction';
    $args         = ['0x512F366D524157BcF734546eB29a6d687B762255'];

    $encoder->encodeFunctionCall($functionName, $args);
})->throws(Exception::class, 'Function not found in ABI: unknownFunction');

it('should throw an exception if there are no arguments', function () {
    $encoder = new AbiEncoder();

    $functionName = 'vote';
    $args         = [];

    $encoder->encodeFunctionCall($functionName, $args);
})->throws(Exception::class, 'Function with matching arguments not found in ABI: vote');

it('should encode an address', function () {
    $address         = '0xb693449AdDa7EFc015D87944EAE8b7C37EB1690A';
    $expectedPayload = '0x000000000000000000000000b693449adda7efc015d87944eae8b7c37eb1690a';

    $encoded = testPrivateMethod('encodeAddress', $object)->invokeArgs($object, [$address]);

    expect($encoded)->toBe([
        'dynamic' => false,
        'encoded' => $expectedPayload,
    ]);
});

it('should handle encoding an invalid address', function () {
    $address = 'testing';

    testPrivateMethod('encodeAddress', $object)->invokeArgs($object, [$address]);
})->throws(Exception::class, 'Invalid address: testing');

it('should encode a true boolean', function () {
    $expectedPayload = '0x0000000000000000000000000000000000000000000000000000000000000001';

    $encoded = testPrivateMethod('encodeBool', $object)->invokeArgs($object, [true]);

    expect($encoded)->toBe([
        'dynamic' => false,
        'encoded' => $expectedPayload,
    ]);
});

it('should encode a false boolean', function () {
    $expectedPayload = '0x0000000000000000000000000000000000000000000000000000000000000000';

    $encoded = testPrivateMethod('encodeBool', $object)->invokeArgs($object, [false]);

    expect($encoded)->toBe([
        'dynamic' => false,
        'encoded' => $expectedPayload,
    ]);
});

it('should encode an unsigned int', function () {
    $expectedPayload = '0x000000000000000000000000000000000000000000000000000000000bebc200';

    $encoded = testPrivateMethod('encodeNumber', $object)->invokeArgs($object, ['200000000', false]);

    expect($encoded)->toBe([
        'dynamic' => false,
        'encoded' => $expectedPayload,
    ]);
});

it('should encode a signed int', function () {
    $expectedPayload = '0xfffffffffffffffffffffffffffffffffffffffffffffffffffffffff4143e00';

    $encoded = testPrivateMethod('encodeNumber', $object)->invokeArgs($object, ['-200000000', true]);

    expect($encoded)->toBe([
        'dynamic' => false,
        'encoded' => $expectedPayload,
    ]);
});

it('should encode a BigDecimal int', function () {
    $expectedPayload = '0xfffffffffffffffffffffffffffffffffffffffffffffffffffffffff4143e00';

    $encoded = testPrivateMethod('encodeNumber', $object)->invokeArgs($object, [BigDecimal::of('-200000000'), true]);

    expect($encoded)->toBe([
        'dynamic' => false,
        'encoded' => $expectedPayload,
    ]);
});

it('should error when encoding a non-number', function () {
    testPrivateMethod('encodeNumber', $object)->invokeArgs($object, ['testing', true]);
})->throws(Exception::class, 'Invalid number value');

it('should error when encoding a negative unsigned number', function () {
    testPrivateMethod('encodeNumber', $object)->invokeArgs($object, ['-200000000', false]);
})->throws(Exception::class, 'Negative value provided for unsigned integer type');

it('should encode a string', function () {
    $expectedPayload = '0x00000000000000000000000000000000000000000000000000000000000000047465737400000000000000000000000000000000000000000000000000000000';

    $encoded = testPrivateMethod('encodeString', $object)->invokeArgs($object, ['test']);

    expect($encoded)->toBe([
        'dynamic' => true,
        'encoded' => $expectedPayload,
    ]);
});

it('should encode dynamic bytes', function () {
    $expectedPayload = '0x00000000000000000000000000000000000000000000000000000000000000047465737400000000000000000000000000000000000000000000000000000000';

    $param = [
        'name' => 'recipients',
        'type' => 'bytes',
    ];

    $encoded = testPrivateMethod('encodeBytes', $object)->invokeArgs($object, ['0x74657374', $param]);

    expect($encoded)->toBe([
        'dynamic' => true,
        'encoded' => $expectedPayload,
    ]);
});

it('should encode fixed bytes', function () {
    $expectedPayload = '0x7465737400000000000000000000000000000000000000000000000000000000';

    $param = [
        'name' => 'recipients',
        'type' => 'bytes4',
    ];

    $encoded = testPrivateMethod('encodeBytes', $object)->invokeArgs($object, ['0x74657374', $param]);

    expect($encoded)->toBe([
        'dynamic' => false,
        'encoded' => $expectedPayload,
    ]);
});

it('should error when encoding fixed bytes of the wrong size', function () {
    $param = [
        'name' => 'recipients',
        'type' => 'bytes32',
    ];

    testPrivateMethod('encodeBytes', $object)->invokeArgs($object, ['0x74657374', $param]);
})->throws(Exception::class, 'Bytes size mismatch: expected 32, got 4');

it('should encode an array', function () {
    $addresses = [
        '0xb693449AdDa7EFc015D87944EAE8b7C37EB1690A',
        '0xb693449AdDa7EFc015D87944EAE8b7C37EB1690A',
    ];
    $expectedPayload = '0x000000000000000000000000b693449adda7efc015d87944eae8b7c37eb1690a000000000000000000000000b693449adda7efc015d87944eae8b7c37eb1690a';

    $param = [
        'name' => 'recipients',
        'type' => 'address',
    ];

    $encoded = testPrivateMethod('encodeArray', $object)->invokeArgs($object, [$addresses, 2, $param]);

    expect($encoded)->toBe([
        'dynamic' => false,
        'encoded' => $expectedPayload,
    ]);
});

it('should encode a tuple', function () {
    $tuple = [
        'from' => '0xb693449AdDa7EFc015D87944EAE8b7C37EB1690A',
        'to'   => '0xb693449AdDa7EFc015D87944EAE8b7C37EB1690A',
    ];
    $expectedPayload = '0x000000000000000000000000b693449adda7efc015d87944eae8b7c37eb1690a000000000000000000000000b693449adda7efc015d87944eae8b7c37eb1690a';

    $param = [
        'name'         => 'recipients',
        'type'         => 'address',
        'components'   => [
            'from' => [
                'name' => 'from',
                'type' => 'address',
            ],
            'to' => [
                'name' => 'to',
                'type' => 'address',
            ],
        ],
    ];

    $encoded = testPrivateMethod('encodeTuple', $object)->invokeArgs($object, [$tuple, $param]);

    expect($encoded)->toBe([
        'dynamic' => false,
        'encoded' => $expectedPayload,
    ]);
});

it('should error when encoding with a tuple missing component', function () {
    $tuple = [
        '0xb693449AdDa7EFc015D87944EAE8b7C37EB1690A',
    ];

    $param = [
        'name'         => 'recipients',
        'type'         => 'address',
        'components'   => [
            'from' => [
                'name' => 'from',
                'type' => 'address',
            ],
        ],
    ];

    testPrivateMethod('encodeTuple', $object)->invokeArgs($object, [$tuple, $param]);
})->throws(Exception::class, 'Tuple value missing component: from');

it('should error for missing function name when preparing function data', function () {
    $param = [
        ...json_decode(file_get_contents(dirname(dirname(dirname(__DIR__))).'/src/Utils/Abi/json/Abi.Consensus.json'), true),

        'type' => 'function',
    ];

    testPrivateMethod('prepareEncodeFunctionData', $object)->invokeArgs($object, [$param]);
})->throws(Exception::class, 'Function name is not provided and ABI has multiple functions');

it('should handle a single function when preparing function data', function () {
    $param = [
        ...json_decode(file_get_contents(dirname(dirname(__DIR__)).'/fixtures/mock-abi.json'), true),

        'type' => 'function',
        'args' => [1],
    ];

    $encoded = testPrivateMethod('prepareEncodeFunctionData', $object)->invokeArgs($object, [$param]);

    expect($encoded)->toBe([
        [
            'type'   => 'function',
            'name'   => 'UPGRADE_INTERFACE_VERSION',
            'inputs' => [
                [
                    'name'         => 'count',
                    'type'         => 'uint256',
                    'internalType' => 'uint256',
                ],
            ],
            'outputs' => [
                [
                    'name'         => '',
                    'type'         => 'string',
                    'internalType' => 'string',
                ],
            ],
            'stateMutability' => 'view',
        ],
        '0xb8cafc0b',
    ]);
});

it('should handle array param types', function () {
    $method = testPrivateMethod('prepareParam', $object);

    $param = [
        'name'         => 'text',
        'type'         => 'int[]',
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

    $result = $method->invokeArgs($object, [
        $param,
        [
            10000,
            20000,
        ],
    ]);

    expect($result)->toBe([
        'dynamic' => true,
        'encoded' => '0x000000000000000000000000000000000000000000000000000000000000000200000000000000000000000000000000000000000000000000000000000027100000000000000000000000000000000000000000000000000000000000004e20',
    ]);
});

it('should handle invalid array type in param types', function () {
    $method = testPrivateMethod('prepareParam', $object);

    $param = [
        'name'         => 'text',
        'type'         => 'int[]',
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

    $method->invokeArgs($object, [
        $param,
        'test',
    ]);
})->throws(Exception::class, 'Invalid array value');

it('should handle wrong array length in param types', function () {
    $method = testPrivateMethod('prepareParam', $object);

    $param = [
        'name' => 'text',
        'type' => 'int[2]',
    ];

    $method->invokeArgs($object, [
        $param,
        [
            100,
            200,
            300,
        ],
    ]);
})->throws(Exception::class, 'Array length mismatch');

it('should handle dynamic array types param types', function () {
    $method = testPrivateMethod('prepareParam', $object);

    $param = [
        'name'         => 'text',
        'type'         => 'string[2]',
    ];

    $result = $method->invokeArgs($object, [
        $param,
        [
            'test',
            'testing',
        ],
    ]);

    expect($result)->toBe([
        'dynamic' => true,
        'encoded' => '0x0000000000000000000000000000000000000000000000000000000000000040000000000000000000000000000000000000000000000000000000000000008000000000000000000000000000000000000000000000000000000000000000047465737400000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000774657374696e6700000000000000000000000000000000000000000000000000',
    ]);
});

it('should handle string param types', function () {
    $method = testPrivateMethod('prepareParam', $object);

    $param = [
        'name' => 'isActive',
        'type' => 'string',
    ];

    $result = $method->invokeArgs($object, [
        $param,
        'test',
    ]);

    expect($result)->toBe([
        'dynamic' => true,
        'encoded' => '0x00000000000000000000000000000000000000000000000000000000000000047465737400000000000000000000000000000000000000000000000000000000',
    ]);
});

it('should handle boolean param types', function () {
    $method = testPrivateMethod('prepareParam', $object);

    $param = [
        'name' => 'isActive',
        'type' => 'bool',
    ];

    $result = $method->invokeArgs($object, [
        $param,
        true,
    ]);

    expect($result)->toBe([
        'dynamic' => false,
        'encoded' => '0x0000000000000000000000000000000000000000000000000000000000000001',
    ]);

    $result = $method->invokeArgs($object, [
        $param,
        false,
    ]);

    expect($result)->toBe([
        'dynamic' => false,
        'encoded' => '0x0000000000000000000000000000000000000000000000000000000000000000',
    ]);
});

it('should handle bytes param types', function () {
    $method = testPrivateMethod('prepareParam', $object);

    $param = [
        'name' => 'text',
        'type' => 'bytes',
    ];

    $result = $method->invokeArgs($object, [
        $param,
        '74657374',
    ]);

    expect($result)->toBe([
        'dynamic' => true,
        'encoded' => '0x00000000000000000000000000000000000000000000000000000000000000036573740000000000000000000000000000000000000000000000000000000000',
    ]);
});

it('should handle dynamic byte length param types', function () {
    $method = testPrivateMethod('prepareParam', $object);

    $param = [
        'name' => 'text',
        'type' => 'bytes4',
    ];

    $result = $method->invokeArgs($object, [
        $param,
        '0x74657374',
    ]);

    expect($result)->toBe([
        'dynamic' => false,
        'encoded' => '0x7465737400000000000000000000000000000000000000000000000000000000',
    ]);
});

it('should handle signed integer param types', function () {
    $method = testPrivateMethod('prepareParam', $object);

    $param = [
        'name' => 'text',
        'type' => 'int256',
    ];

    $result = $method->invokeArgs($object, [
        $param,
        -200000000,
    ]);

    expect($result)->toBe([
        'dynamic' => false,
        'encoded' => '0xfffffffffffffffffffffffffffffffffffffffffffffffffffffffff4143e00',
    ]);
});

it('should handle unsigned integer param types', function () {
    $method = testPrivateMethod('prepareParam', $object);

    $param = [
        'name' => 'text',
        'type' => 'uint256',
    ];

    $result = $method->invokeArgs($object, [
        $param,
        200000000,
    ]);

    expect($result)->toBe([
        'dynamic' => false,
        'encoded' => '0x000000000000000000000000000000000000000000000000000000000bebc200',
    ]);
});

it('should handle tuple param types', function () {
    $method = testPrivateMethod('prepareParam', $object);

    $param = [
        'name'         => 'text',
        'type'         => 'tuple',
        'components'   => [
            'from' => [
                'name' => 'from',
                'type' => 'address',
            ],
            'to' => [
                'name' => 'to',
                'type' => 'address',
            ],
        ],
    ];

    $result = $method->invokeArgs($object, [
        $param,
        [
            'from' => '0xb693449AdDa7EFc015D87944EAE8b7C37EB1690A',
            'to'   => '0xb693449AdDa7EFc015D87944EAE8b7C37EB1690A',
        ],
    ]);

    expect($result)->toBe([
        'dynamic' => false,
        'encoded' => '0x000000000000000000000000b693449adda7efc015d87944eae8b7c37eb1690a000000000000000000000000b693449adda7efc015d87944eae8b7c37eb1690a',
    ]);
});

it('should handle dynamic tuple types param types', function () {
    $method = testPrivateMethod('prepareParam', $object);

    $param = [
        'name'         => 'text',
        'type'         => 'tuple',
        'components'   => [
            [
                'name' => 'recipient',
                'type' => 'string',
            ],
        ],
    ];

    $result = $method->invokeArgs($object, [
        $param,
        [
            'recipient' => 'test',
        ],
    ]);

    expect($result)->toBe([
        'dynamic' => true,
        'encoded' => '0x000000000000000000000000000000000000000000000000000000000000002000000000000000000000000000000000000000000000000000000000000000047465737400000000000000000000000000000000000000000000000000000000',
    ]);
});

it('should handle unknown param type', function () {
    $method = testPrivateMethod('prepareParam', $object);

    $param = [
        'name' => 'isActive',
        'type' => 'testing',
    ];

    $method->invokeArgs($object, [
        $param,
        'test',
    ]);
})->throws(Exception::class, 'Invalid ABI type: testing');
