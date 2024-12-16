<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto;

use ArkEcosystem\Crypto\Configuration\Network;
use ArkEcosystem\Crypto\Exceptions\InvalidUsernameException;

class Helpers
{
    /**
     * Get the network version.
     *
     * @param Networks\AbstractNetwork|int $network
     *
     * @return int
     */
    public static function version($network): int
    {
        return is_int($network) ? $network : $network->version();
    }

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
}
