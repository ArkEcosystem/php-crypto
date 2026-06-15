<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto;

use ArkEcosystem\Crypto\Exceptions\InvalidUsernameException;

class Helpers
{
    // Based on https://github.com/ArkEcosystem/mainsail/blob/develop/contracts/src/usernames/UsernamesV1.sol#L101
    public static function isValidUsername(string $username): bool
    {
        if (strlen($username) < 1 || strlen($username) > 20) {
            throw new InvalidUsernameException(
                sprintf(
                    'Username must be between 1 and 20 characters long. Got %d characters.',
                    strlen($username)
                )
            );
        }

        // Only lowercase letters, numbers and underscores are allowed
        if (preg_match('/[^a-z0-9_]/', $username)) {
            throw new InvalidUsernameException(
                'Username can only contain lowercase letters, numbers and underscores.'
            );
        }

        // Cannot start or end with underscore
        if (preg_match('/^_|_$/', $username)) {
            throw new InvalidUsernameException(
                'Username cannot start or end with an underscore.'
            );
        }

        // Cannot contain two or more consecutive underscores
        if (preg_match('/__/', $username)) {
            throw new InvalidUsernameException(
                'Username cannot contain consecutive underscores.'
            );
        }

        return true;
    }

    public static function removeLeadingHexZero(string $hex): string
    {
        return preg_replace('/^0x/', '', $hex); // using ltrim($hex, '0x') also removes leading 0s which is not desired, e.g. 0x0123 -> 123
    }

    public static function gmpToHex(\GMP $gmp): string
    {
        $hex = gmp_strval($gmp, 16);

        return str_pad($hex, 64, '0', STR_PAD_LEFT);
    }
}
