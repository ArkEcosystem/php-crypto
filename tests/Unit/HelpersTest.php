<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Unit;

use ArkEcosystem\Crypto\Exceptions\InvalidUsernameException;
use ArkEcosystem\Crypto\Helpers;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * @covers \ArkEcosystem\Crypto\Helpers
 */
class HelpersTest extends TestCase
{
    /** @test */
    public function it_should_trim_hex_values_properly(): void
    {
        $this->assertEquals('0123', Helpers::removeLeadingHexZero('0x0123'));
        $this->assertEquals('0123', Helpers::removeLeadingHexZero('0123'));
        $this->assertEquals('1234', Helpers::removeLeadingHexZero('0x1234'));
        $this->assertEquals('0000', Helpers::removeLeadingHexZero('0x0000'));
    }

    /**
     * @test
     * @dataProvider validUsernamesProvider
     */
    public function it_accepts_valid_usernames(string $username): void
    {
        try {
            Helpers::isValidUsername($username);
            $this->assertTrue(true); // If we get here, no exception was thrown
        } catch (InvalidUsernameException $e) {
            $this->fail('Valid username threw an exception: '.$e->getMessage());
        }
    }

    /**
     * @test
     * @dataProvider invalidLengthUsernamesProvider
     */
    public function it_rejects_usernames_with_invalid_length(string $username): void
    {
        $this->expectException(InvalidUsernameException::class);
        $this->expectExceptionMessage('Username must be between 1 and 20 characters long');

        Helpers::isValidUsername($username);
    }

    /**
     * @test
     * @dataProvider invalidCharacterUsernamesProvider
     */
    public function it_rejects_usernames_with_invalid_characters(string $username): void
    {
        $this->expectException(InvalidUsernameException::class);
        $this->expectExceptionMessage('Username can only contain lowercase letters, numbers and underscores');

        Helpers::isValidUsername($username);
    }

    /**
     * @test
     * @dataProvider usernamesWithUnderscoreBoundariesProvider
     */
    public function it_rejects_usernames_starting_or_ending_with_underscore(string $username): void
    {
        $this->expectException(InvalidUsernameException::class);
        $this->expectExceptionMessage('Username cannot start or end with an underscore');

        Helpers::isValidUsername($username);
    }

    /**
     * @test
     * @dataProvider usernamesWithConsecutiveUnderscoresProvider
     */
    public function it_rejects_usernames_with_consecutive_underscores(string $username): void
    {
        $this->expectException(InvalidUsernameException::class);
        $this->expectExceptionMessage('Username cannot contain consecutive underscores');

        Helpers::isValidUsername($username);
    }

    public function validUsernamesProvider(): array
    {
        return [
            'simple username'                 => ['john'],
            'username with numbers'           => ['john123'],
            'username with single underscore' => ['john_doe'],
            'minimum length'                  => ['a'],
            'maximum length'                  => ['abcdefghijklmnopqrst'], // 20 characters
            'mixed characters'                => ['user_123_name'],
        ];
    }

    public function invalidLengthUsernamesProvider(): array
    {
        return [
            'empty string' => [''],
            'too long'     => ['abcdefghijklmnopqrstu'], // 21 characters
        ];
    }

    public function invalidCharacterUsernamesProvider(): array
    {
        return [
            'uppercase letters'    => ['John'],
            'special characters'   => ['john@doe'],
            'spaces'               => ['john doe'],
            'non-ASCII characters' => ['jöhn'],
        ];
    }

    public function usernamesWithUnderscoreBoundariesProvider(): array
    {
        return [
            'starting underscore' => ['_john'],
            'ending underscore'   => ['john_'],
            'both underscores'    => ['_john_'],
        ];
    }

    public function usernamesWithConsecutiveUnderscoresProvider(): array
    {
        return [
            'double underscore'           => ['john__doe'],
            'triple underscore'           => ['john___doe'],
            'multiple double underscores' => ['john__doe__smith'],
        ];
    }
}
