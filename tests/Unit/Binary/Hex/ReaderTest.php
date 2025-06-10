<?php

use ArkEcosystem\Crypto\Binary\Hex\Reader;

// Tests for the low() method - swaps nibbles within each byte, lowercase
test('low swaps nibbles within each byte and returns lowercase', function () {
    $data = "\x12\x34";
    $result = Reader::low($data, 0, 4); // Read 4 nibbles (2 bytes)
    expect($result)->toBe('2143'); // 0x12 becomes '21', 0x34 becomes '43'
});

test('low reads single nibble by default', function () {
    $data = "\x12\x34";
    $result = Reader::low($data);
    expect($result)->toBe('2'); // Just the low nibble of first byte
});

// Tests for the high() method - normal nibble order, uppercase
test('high keeps normal nibble order and returns uppercase', function () {
    $data = "\x12\x34";
    $result = Reader::high($data, 0, 4); // Read 4 nibbles (2 bytes)
    expect($result)->toBe('1234'); // 0x12 stays '12', 0x34 stays '34'
});

test('high reads single nibble by default', function () {
    $data = "\x12\x34";
    $result = Reader::high($data);
    expect($result)->toBe('1'); // Just the high nibble of first byte
});
