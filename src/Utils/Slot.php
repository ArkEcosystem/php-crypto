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

namespace ArkEcosystem\Crypto\Utils;

use ArkEcosystem\Crypto\Configuration\Network;

/**
 * This is the slot class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class Slot
{
    /**
     * Get the time diff between now and network start.
     *
     * @return int
     */
    public static function time(): int
    {
        return time() - static::epoch();
    }

    /**
     * Get the network start epoch.
     *
     * @return int
     */
    public static function epoch(): int
    {
        return strtotime(Network::epoch());
    }
}
