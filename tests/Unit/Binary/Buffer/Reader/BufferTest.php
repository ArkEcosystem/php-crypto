<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\Binary\Buffer\Reader\Buffer;

it('creates a buffer from hex', function () {
    $hex    = 'deadbeef';
    $buffer = Buffer::fromHex($hex);

    expect($buffer)->toBeInstanceOf(Buffer::class);
    expect($buffer->toHex())->toBe($hex);
    expect($buffer->toBinary())->toBe(hex2bin($hex));
});

it('sets position and updates bytes', function () {
    $hex    = 'deadbeef';
    $buffer = Buffer::fromHex($hex)->position(2);

    $expectedBytes = substr(hex2bin($hex), 2);

    expect($buffer->toBinary())->toBe($expectedBytes);
});

it('skips bytes correctly', function () {
    $hex    = 'deadbeef';
    $buffer = Buffer::fromHex($hex)->position(1)->skip(1);

    $expectedBytes = substr(hex2bin($hex), 2);

    expect($buffer->toBinary())->toBe($expectedBytes);
});

it('returns binary representation', function () {
    $hex    = 'cafebabe';
    $buffer = Buffer::fromHex($hex);

    expect($buffer->toBinary())->toBe(hex2bin($hex));
});

it('returns hex representation', function () {
    $hex    = 'cafebabe';
    $buffer = Buffer::fromHex($hex);

    expect($buffer->toHex())->toBe($hex);
});

it('handles empty hex string', function () {
    $hex    = '';
    $buffer = Buffer::fromHex($hex);

    expect($buffer->toHex())->toBe('')
        ->and($buffer->toBinary())->toBe('');
});

it('position beyond length returns empty bytes', function () {
    $hex    = 'deadbeef';
    $buffer = Buffer::fromHex($hex)->position(100);

    expect($buffer->toBinary())->toBe('');
});

it('skip beyond length returns empty bytes', function () {
    $hex    = 'deadbeef';
    $buffer = Buffer::fromHex($hex)->skip(100);

    expect($buffer->toBinary())->toBe('');
});

it('multiple position and skip calls work as expected', function () {
    $hex    = 'deadbeefcafebabe';
    $buffer = Buffer::fromHex($hex)->position(2)->skip(3);

    $expectedBytes = substr(hex2bin($hex), 5);

    expect($buffer->toBinary())->toBe($expectedBytes);
});

it('reads hex correctly with readHex', function () {
    $hex    = 'deadbeefcafebabe';
    $buffer = Buffer::fromHex($hex);

    $readHex = $buffer->readHex(4);
    expect($readHex)->toBe('deadbeef');

    // After reading, the buffer should have advanced by 4 bytes (8 hex chars)
    $remainingHex = substr($hex, 8);

    expect($buffer->toHex())->toBe($hex); // hex property remains unchanged
    expect($buffer->toBinary())->toBe(hex2bin($remainingHex));
});

it('reads hex bytes correctly with readHexBytes', function () {
    $hex    = 'deadbeefcafebabe';
    $buffer = Buffer::fromHex($hex);

    $readHex = $buffer->readHexBytes(4);
    expect($readHex)->toBe(hex2bin('dead'));
});

it('reads raw hex correctly with readHexRaw', function () {
    $hex    = 'deadbeefcafebabe';
    $buffer = Buffer::fromHex($hex);

    $readHex = $buffer->readHexRaw(4);
    expect($readHex)->toBe('dead');
});

it('should read int 8', function () {
    $hex    = 'ff'; // -1 in signed 8-bit
    $buffer = Buffer::fromHex($hex);

    $value = $buffer->readInt8();
    expect($value)->toBe(-1);
});

it('should read int 16', function () {
    $hex    = 'ffff'; // -1 in signed 16-bit
    $buffer = Buffer::fromHex($hex);

    $value = $buffer->readInt16();
    expect($value)->toBe(-1);
});

it('should read int 32', function () {
    $hex    = 'ffffffff'; // -1 in signed 32-bit
    $buffer = Buffer::fromHex($hex);

    $value = $buffer->readInt32();
    expect($value)->toBe(-1);
});

it('should read int 64', function () {
    $hex    = 'ffffffffffffffff'; // -1 in signed 64-bit
    $buffer = Buffer::fromHex($hex);

    $value = $buffer->readInt64();
    expect($value)->toBe(-1);
});

it('should read unsigned int 8', function () {
    $hex    = 'ff'; // 255 in unsigned 8-bit
    $buffer = Buffer::fromHex($hex);

    $value = $buffer->readUInt8();
    expect($value)->toBe(255);
});

it('should read unsigned int 16', function () {
    $hex    = 'ffff'; // 65535 in unsigned 16-bit
    $buffer = Buffer::fromHex($hex);

    $value = $buffer->readUInt16();
    expect($value)->toBe(65535);
});

it('should read unsigned int 32', function () {
    $hex    = 'ffffffff'; // 4294967295 in unsigned 32-bit
    $buffer = Buffer::fromHex($hex);

    $value = $buffer->readUInt32();
    expect($value)->toBe(4294967295);
});

it('should read unsigned int 64 as string', function () {
    $hex    = 'FFFFFFFFFFFFFFDD';
    $buffer = Buffer::fromHex($hex);

    $value = $buffer->readUInt64();
    expect($value)->toBe('15996785876420001791');
});
