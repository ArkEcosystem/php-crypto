<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\Binary\UnsignedInteger\Writer;

it('should write an unsigned 8 bit positive integer', function () {
    $data   = 127;
    $result = Writer::bit8($data);
    expect($result)->toBe(pack('c', $data));
});

it('should write an unsigned 16 bit positive integer', function () {
    $data   = 32767;
    $result = Writer::bit16($data);
    expect($result)->toBe(pack('s', $data));
});

it('should write an unsigned 16 bit positive integer with big endian', function () {
    $data   = 32767;
    $result = Writer::bit16($data, true);
    expect($result)->toBe(pack('n', $data));
});

it('should write an unsigned 16 bit positive integer as machine-byte order', function () {
    $data   = 32767;
    $result = Writer::bit16($data, null);
    expect($result)->toBe(pack('S', $data));
});

it('should write an unsigned 32 bit positive integer', function () {
    $data   = 2147483647;
    $result = Writer::bit32($data);
    expect($result)->toBe(pack('l', $data));
});

it('should write an unsigned 32 bit positive integer with big endian', function () {
    $data   = 2147483647;
    $result = Writer::bit32($data, true);
    expect($result)->toBe(pack('N', $data));
});

it('should write an unsigned 32 bit positive integer as machine-byte order', function () {
    $data   = 2147483647;
    $result = Writer::bit32($data, null);
    expect($result)->toBe(pack('L', $data));
});

it('should write an unsigned 64 bit positive integer', function () {
    $result = Writer::bit64(9223372036854775807);
    expect(bin2hex($result))->toBe('ffffffffffffff7f');
});

it('should write a max unsigned 64 bit positive integer (with little endian)', function () {
    $result = Writer::bit64('18446744073709551615');
    expect(bin2hex($result))->toBe('ffffffffffffffff');
});

it('should write an unsigned 64 bit positive integer with big endian', function () {
    $result = Writer::bit64('18446744073709551581', true);
    expect(bin2hex($result))->toBe('ffffffffffffffdd');
});
