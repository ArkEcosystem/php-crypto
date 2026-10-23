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

use Elliptic\EC;

/**
 * Shared secp256k1 curve instance.
 *
 * Building the curve precomputes lookup tables, so a single instance is
 * reused for every key and signature operation.
 */
class Secp256k1
{
    /**
     * @var EC|null
     */
    private static $curve;

    /**
     * Get the secp256k1 curve.
     *
     * @return EC
     */
    public static function curve(): EC
    {
        return static::$curve ??= new EC('secp256k1');
    }
}
