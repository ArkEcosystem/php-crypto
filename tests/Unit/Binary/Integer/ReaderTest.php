<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\Binary\Integer\Reader;

it('should read a signed 8 bit negative integer', function () {
    $data   = pack('c', -128);
    $result = Reader::bit8($data);
    expect($result)->toBe(-128);
});

it('should read a signed 8 bit positive integer', function () {
    $data   = pack('c', 127);
    $result = Reader::bit8($data);
    expect($result)->toBe(127);
});

it('should read a signed 16 bit negative integer', function () {
    $data   = pack('s', -32768);
    $result = Reader::bit16($data);
    expect($result)->toBe(-32768);
});

it('should read a signed 16 bit positive integer', function () {
    $data   = pack('s', 32767);
    $result = Reader::bit16($data);
    expect($result)->toBe(32767);
});

it('should read a signed 32 bit negative integer', function () {
    $data   = pack('l', -2147483648);
    $result = Reader::bit32($data);
    expect($result)->toBe(-2147483648);
});

it('should read a signed 32 bit positive integer', function () {
    $data   = pack('l', 2147483647);
    $result = Reader::bit32($data);
    expect($result)->toBe(2147483647);
});

it('should read a signed 64 bit negative integer', function () {
    $data   = pack('q', -9223372036854775807);
    $result = Reader::bit64($data);
    expect($result)->toBe(-9223372036854775807);
});

it('should read a signed 64 bit positive integer', function () {
    $data   = pack('q', 9223372036854775807);
    $result = Reader::bit64($data);
    expect($result)->toBe(9223372036854775807);
});
