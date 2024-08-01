<?php

declare(strict_types=1);



namespace ArkEcosystem\Crypto;

use ArkEcosystem\Crypto\Configuration\Network;

/**
 * This is the helpers class.
 *
 * @author Brian Faust <brian@ark.io>
 */
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
}
