<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\Binary\Buffer\Writer\Buffer;

it('creates a buffer from hex and writes correctly', function () {
    $hex    = 'deadbeef';
    $buffer = (new Buffer())->writeHex($hex);

    expect($buffer)->toBeInstanceOf(Buffer::class);
    expect($buffer->toHex())->toBe($hex);
    expect($buffer->toBytes())->toBe(hex2bin($hex));
});

it('returns binary representation after writing', function () {
    $hex    = 'cafebabe';
    $buffer = (new Buffer())->writeHex($hex);

    expect($buffer->toBytes())->toBe(hex2bin($hex));
});

it('returns hex representation after writing', function () {
    $hex    = 'cafebabe';
    $buffer = (new Buffer())->writeHex($hex);

    expect($buffer->toHex())->toBe($hex);
});

it('writes hex correctly with writeHex', function () {
    $hex    = 'deadbeefcafebabe';
    $buffer = (new Buffer())->writeHex($hex);

    expect($buffer->toHex())->toBe($hex);
    expect($buffer->toBytes())->toBe(hex2bin($hex));
});

it('writes hex bytes correctly with writeHexBytes', function () {
    $hex    = 'deadbeefcafebabe';
    $buffer = (new Buffer())->writeHexBytes($hex);

    expect($buffer->toBytes())->toBe(hex2bin($hex));
});

it('should write int 8', function () {
    $buffer = (new Buffer())->writeInt8('-1');

    expect($buffer->toHex())->toBe('ff');
});

it('should write int 16', function () {
    $buffer = (new Buffer())->writeInt16(-1);

    expect($buffer->toHex())->toBe('ffff');
});

it('should write int 32', function () {
    $buffer = (new Buffer())->writeInt32(-1);

    expect($buffer->toHex())->toBe('ffffffff');
});

it('should write int 64', function () {
    $buffer = (new Buffer())->writeInt64('-1');

    expect($buffer->toHex())->toBe('ffffffffffffffff');
});

it('should write unsigned int 8', function () {
    $buffer = (new Buffer())->writeUInt8(255);

    expect($buffer->toHex())->toBe('ff');
});

it('should write unsigned int 16', function () {
    $buffer = (new Buffer())->writeUInt16(65535);

    expect($buffer->toHex())->toBe('ffff');
});

it('should write unsigned int 32', function () {
    $buffer = (new Buffer())->writeUInt32(4294967295);

    expect($buffer->toHex())->toBe('ffffffff');
});

it('should write unsigned int 64 as string', function () {
    $buffer = (new Buffer())->writeUInt64('18446744073709551581');

    expect($buffer->toHex())->toBe('ddffffffffffffff');
});

it('writes string correctly with writeString', function () {
    $string = 'hello world';
    $hex    = bin2hex($string);
    $buffer = (new Buffer())->writeString($string);

    expect($buffer->toBytes())->toBe($string);
    expect($buffer->toHex())->toBe($hex);
});

it('writes empty string with writeString', function () {
    $string = '';
    $buffer = (new Buffer())->writeString($string);

    expect($buffer->toBytes())->toBe('');
    expect($buffer->toHex())->toBe('');
});

it('writes multibyte string with writeString', function () {
    $string = 'こんにちは'; // "Hello" in Japanese
    $hex    = bin2hex($string);
    $buffer = (new Buffer())->writeString($string);

    expect($buffer->toBytes())->toBe($string);
    expect($buffer->toHex())->toBe($hex);
});

it('fills the buffer with zero bytes', function () {
    $buffer = new Buffer();
    $buffer->fill(4);

    expect($buffer->toBytes())->toBe(str_repeat("\x00", 4));
    expect($buffer->toHex())->toBe('00000000');
});
