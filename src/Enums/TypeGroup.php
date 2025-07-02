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

namespace ArkEcosystem\Crypto\Enums;

/**
 * This is the transaction type group class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class TypeGroup
{
    public const TEST = 0;

    public const CORE = 1;

    public const RESERVED = 1000; // Everything above is available to anyone
}
