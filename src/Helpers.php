<?php

declare(strict_types=1);

/*
 * This file is part of Ark PHP Crypto.
 *
 * (c) Ark Ecosystem <info@ark.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
     * @param \ArkEcosystem\Crypto\Networks\AbstractNetwork|int $network
     *
     * @return int
     */
    public static function version($network): int
    {
        return is_int($network) ? $network : $network->version();
    }
}
