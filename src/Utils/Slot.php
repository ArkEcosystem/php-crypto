<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Utils;

use ArkEcosystem\Crypto\Configuration\Network;

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
