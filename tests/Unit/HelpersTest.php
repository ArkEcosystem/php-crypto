<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\Exceptions\InvalidUsernameException;
use ArkEcosystem\Crypto\Helpers;

// Test for trimming hex values
test('it should trim hex values properly', function () {
    expect(Helpers::removeLeadingHexZero('0x0123'))->toBe('0123');
    expect(Helpers::removeLeadingHexZero('0123'))->toBe('0123');
    expect(Helpers::removeLeadingHexZero('0x1234'))->toBe('1234');
    expect(Helpers::removeLeadingHexZero('0x0000'))->toBe('0000');
});

// Tests for valid usernames
dataset('valid_usernames', [
    'simple username'                 => ['john'],
    'username with numbers'           => ['john123'],
    'username with single underscore' => ['john_doe'],
    'minimum length'                  => ['a'],
    'maximum length'                  => ['abcdefghijklmnopqrst'], // 20 characters
    'mixed characters'                => ['user_123_name'],
]);

test('it accepts valid usernames', function (string $username) {
    try {
        Helpers::isValidUsername($username);
        expect(true)->toBeTrue(); // If we get here, no exception was thrown
    } catch (InvalidUsernameException $e) {
        $this->fail('Valid username threw an exception: '.$e->getMessage());
    }
})->with('valid_usernames');

// Tests for invalid username length
dataset('invalid_length_usernames', [
    'empty string' => [''],
    'too long'     => ['abcdefghijklmnopqrstu'], // 21 characters
]);

test('it rejects usernames with invalid length', function (string $username) {
    $this->expectException(InvalidUsernameException::class);
    $this->expectExceptionMessage('Username must be between 1 and 20 characters long');

    Helpers::isValidUsername($username);
})->with('invalid_length_usernames');

// Tests for invalid username characters
dataset('invalid_character_usernames', [
    'uppercase letters'    => ['John'],
    'special characters'   => ['john@doe'],
    'spaces'               => ['john doe'],
    'non-ASCII characters' => ['jöhn'],
]);

test('it rejects usernames with invalid characters', function (string $username) {
    $this->expectException(InvalidUsernameException::class);
    $this->expectExceptionMessage('Username can only contain lowercase letters, numbers and underscores');

    Helpers::isValidUsername($username);
})->with('invalid_character_usernames');

// Tests for usernames with underscore boundaries
dataset('usernames_with_underscore_boundaries', [
    'starting underscore' => ['_john'],
    'ending underscore'   => ['john_'],
    'both underscores'    => ['_john_'],
]);

test('it rejects usernames starting or ending with underscore', function (string $username) {
    $this->expectException(InvalidUsernameException::class);
    $this->expectExceptionMessage('Username cannot start or end with an underscore');

    Helpers::isValidUsername($username);
})->with('usernames_with_underscore_boundaries');

// Tests for usernames with consecutive underscores
dataset('usernames_with_consecutive_underscores', [
    'double underscore'           => ['john__doe'],
    'triple underscore'           => ['john___doe'],
    'multiple double underscores' => ['john__doe__smith'],
]);

test('it rejects usernames with consecutive underscores', function (string $username) {
    $this->expectException(InvalidUsernameException::class);
    $this->expectExceptionMessage('Username cannot contain consecutive underscores');

    Helpers::isValidUsername($username);
})->with('usernames_with_consecutive_underscores');
