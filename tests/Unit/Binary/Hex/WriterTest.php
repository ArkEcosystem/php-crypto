<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\Binary\Hex\Writer;

// Tests for the Writer::low() method - converts low nibble first hex strings to binary
test('low converts single byte without nibble count specified', function () {
    $hexData = '21'; // Low nibble first: 21 = 0x12
    $result  = Writer::low($hexData);
    expect($result)->toBe("\x12");
});

test('low converts low nibble first hex string to binary without nibble count specified', function () {
    $hexData = '2143'; // Low nibble first format: 21 = 0x12, 43 = 0x34
    $result  = Writer::low($hexData);
    expect($result)->toBe("\x12\x34");
});

test('low converts low nibble first hex string to binary', function () {
    $hexData = '2143'; // Low nibble first format: 21 = 0x12, 43 = 0x34
    $result  = Writer::low($hexData, 4);
    expect($result)->toBe("\x12\x34");
});

test('low converts hex string with specified nibble count to binary', function () {
    $hexData = '214365'; // 2143 = 0x1234, 65 = 0x56 (but only read 4 nibbles)
    $result  = Writer::low($hexData, 4);
    expect($result)->toBe("\x12\x34");
});

// Tests for the Writer::high() method - converts high nibble first hex strings to binary
test('high converts single byte without nibble count specified', function () {
    $hexData = '12'; // High nibble first: 12 = 0x12
    $result  = Writer::high($hexData);
    expect($result)->toBe("\x12");
});

test('high converts high nibble first hex string to binarywithout nibble count specified', function () {
    $hexData = '1234'; // High nibble first format: 12 = 0x12, 34 = 0x34
    $result  = Writer::high($hexData);
    expect($result)->toBe("\x12\x34");
});

test('high converts high nibble first hex string to binary', function () {
    $hexData = '1234'; // High nibble first format: 12 = 0x12, 34 = 0x34
    $result  = Writer::high($hexData, 4);
    expect($result)->toBe("\x12\x34");
});

test('high converts hex string with specified nibble count to binary', function () {
    $hexData = '123456'; // 1234 = 0x1234, 56 = 0x56 (but only read 4 nibbles)
    $result  = Writer::high($hexData, 4);
    expect($result)->toBe("\x12\x34");
});
