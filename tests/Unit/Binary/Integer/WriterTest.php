<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\Binary\Integer\Writer;

it('should write a signed 8 bit negative integer', function () {
    $data   = -128;
    $result = Writer::bit8($data);
    expect($result)->toBe(pack('c', $data));
});

it('should write a signed 8 bit positive integer', function () {
    $data   = 127;
    $result = Writer::bit8($data);
    expect($result)->toBe(pack('c', $data));
});

it('should write a signed 16 bit negative integer', function () {
    $data   = -32768;
    $result = Writer::bit16($data);
    expect($result)->toBe(pack('s', $data));
});

it('should write a signed 16 bit positive integer', function () {
    $data   = 32767;
    $result = Writer::bit16($data);
    expect($result)->toBe(pack('s', $data));
});

it('should write a signed 32 bit negative integer', function () {
    $data   = -2147483648;
    $result = Writer::bit32($data);
    expect($result)->toBe(pack('l', $data));
});

it('should write a signed 32 bit positive integer', function () {
    $data   = 2147483647;
    $result = Writer::bit32($data);
    expect($result)->toBe(pack('l', $data));
});

it('should write a signed 64 bit negative integer', function () {
    $data   = -9223372036854775807;
    $result = Writer::bit64($data);
    expect($result)->toBe(pack('q', $data));
});

it('should write a signed 64 bit positive integer', function () {
    $data   = 9223372036854775807;
    $result = Writer::bit64($data);
    expect($result)->toBe(pack('q', $data));
});
