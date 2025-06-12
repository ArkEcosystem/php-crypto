<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\Binary\UnsignedInteger\Reader;

it('should read an unsigned 8 bit positive integer', function () {
    $data   = pack('c', 127);
    $result = Reader::bit8($data);
    expect($result)->toBe(127);
});

it('should read an unsigned 16 bit positive integer', function () {
    $data   = pack('v', 32767);
    $result = Reader::bit16($data);
    expect($result)->toBe(32767);
});

it('should read an unsigned 16 bit positive integer with big endian', function () {
    $data   = pack('n', 32767);
    $result = Reader::bit16($data, 0, true);
    expect($result)->toBe(32767);
});

it('should read an unsigned 16 bit positive integer as machine-byte order', function () {
    $data   = pack('S', 32767);
    $result = Reader::bit16($data, 0, null);
    expect($result)->toBe(32767);
});

it('should read an unsigned 32 bit positive integer', function () {
    $data   = pack('l', 2147483647);
    $result = Reader::bit32($data);
    expect($result)->toBe(2147483647);
});

it('should read an unsigned 32 bit positive integer with big endian', function () {
    $data   = pack('N', 2147483647);
    $result = Reader::bit32($data, 0, true);
    expect($result)->toBe(2147483647);
});
it('should read an unsigned 32 bit positive integer as machine-byte order', function () {
    $data   = pack('L', 2147483647);
    $result = Reader::bit32($data, 0, null);
    expect($result)->toBe(2147483647);
});

it('should read an unsigned 64 bit positive integer', function () {
    $data   = hex2bin('FFFFFFFFFFFFFF7F');
    $result = Reader::bit64($data);
    expect($result)->toBe(9223372036854775807);
});

it('should read a max unsigned 64 bit positive integer (with little endian)', function () {
    $data   = hex2bin('DDFFFFFFFFFFFFFF');
    $result = Reader::bit64($data);
    expect($result)->toBe('18446744073709551581');
});

it('should read an unsigned 64 bit positive integer with big endian', function () {
    $data   = hex2bin('FFFFFFFFFFFFFFDD');
    $result = Reader::bit64($data, 0, true);
    expect($result)->toBe('18446744073709551581');
});
