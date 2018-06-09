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

namespace ArkEcosystem\ArkCrypto\Enums;

class TransactionTypes
{
    const TRANSFER             = 0;
    const SECOND_SIGNATURE     = 1;
    const DELEGATE             = 2;
    const VOTE                 = 3;
    const MULTI_SIGNATURE      = 4;
    const IPFS                 = 5;
    const TIMELOCK_TRANSFER    = 6;
    const MULTI_PAYMENT        = 7;
    const DELEGATE_RESIGNATION = 8;
}
