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

class TransactionFees
{
    const TRANSFER             = 10000000;
    const SECOND_SIGNATURE     = 500000000;
    const DELEGATE             = 2500000000;
    const VOTE                 = 100000000;
    const MULTI_SIGNATURE      = 500000000;
    const IPFS                 = 0;
    const TIMELOCK_TRANSFER    = 0;
    const MULTI_PAYMENT        = 0;
    const DELEGATE_RESIGNATION = 0;
}
