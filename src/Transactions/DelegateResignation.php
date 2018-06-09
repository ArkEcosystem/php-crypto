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

namespace ArkEcosystem\ArkCrypto\Transactions;

use ArkEcosystem\ArkCrypto\Enums\TransactionTypes;

class DelegateResignation extends Transaction
{
    /**
     * [__construct description].
     */
    public function __construct()
    {
        parent::__construct();

        $this->type = TransactionTypes::DELEGATE_RESIGNATION;
        $this->fee  = TransactionFees::DELEGATE_RESIGNATION;
    }
}
