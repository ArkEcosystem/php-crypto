<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\Utils\RlpEncoder;

it('should encoding str', function () {
    $encoded = RlpEncoder::encode('testing');

    expect($encoded)->toBe('0x8774657374696e67');
});

it('should encoding bytes', function () {
    $encoded = RlpEncoder::encode(b'testing');

    expect($encoded)->toBe('0x8774657374696e67');
});

it('should encoding list', function () {
    $encoded = RlpEncoder::encode(['testing']);

    expect($encoded)->toBe('0xc88774657374696e67');
});

it('should encoding int', function () {
    $encoded = RlpEncoder::encode('123456');

    expect($encoded)->toBe('0x86313233343536');
});

it('should encode large values', function () {
    $encoded = RlpEncoder::encode(['12345678901234567890123456789012345678901234567890123456789012345678901234567890']);

    expect($encoded)->toBe('0xf852b8503132333435363738393031323334353637383930313233343536373839303132333435363738393031323334353637383930313233343536373839303132333435363738393031323334353637383930');
});

it('should encode large arrays', function () {
    $encoded = RlpEncoder::encode([
        'testing',
        'another test',
        'testing 2',
        'testing 3',
        'testing 4',
        'testing 5',
        'testing 6',
        'testing 7',
        'testing 8',
    ]);

    expect($encoded)->toBe('0xf85b8774657374696e678c616e6f7468657220746573748974657374696e6720328974657374696e6720338974657374696e6720348974657374696e6720358974657374696e6720368974657374696e6720378974657374696e672038');
});

it('should return singular hex values', function () {
    $encoded = RlpEncoder::encode('0x12');

    expect($encoded)->toBe('0x12');
});

it('should encode larger hex values', function () {
    $encoded = RlpEncoder::encode('0x1212');

    expect($encoded)->toBe('0x821212');
});

it('should handle empty hex values', function () {
    $encoded = RlpEncoder::encode('0x');

    expect($encoded)->toBe('0x80');
});

it('should pad odd length hex values', function () {
    $encoded = RlpEncoder::encode('0x1');

    expect($encoded)->toBe('0x01');
});

it('should pad longer odd length hex values', function () {
    $encoded = RlpEncoder::encode('0x121');

    expect($encoded)->toBe('0x820121');
});

it('should handle integer values', function () {
    $encoded = RlpEncoder::encode(0);

    expect($encoded)->toBe('0x80');

    $encoded = RlpEncoder::encode(1);

    expect($encoded)->toBe('0x01');

    $encoded = RlpEncoder::encode(123);

    expect($encoded)->toBe('0x7b');

    $encoded = RlpEncoder::encode(1234567);

    expect($encoded)->toBe('0x8312d687');
});

it('should error for invalid types', function ($type) {
    RlpEncoder::encode($type);
})->with([
    'bool'   => true,
    'float'  => 1.0,
    'object' => new stdClass(),
])->throws(InvalidArgumentException::class, 'invalid type');
