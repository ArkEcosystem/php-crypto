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

namespace ArkEcosystem\Crypto\Transactions;

use ArkEcosystem\Crypto\Enums\TransactionTypes;

/**
 * This is the delegate resignation transaction class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class DelegateResignation extends Transaction
{
    /**
     * Create a new delegate resignation transaction instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->data->type = TransactionTypes::DELEGATE_RESIGNATION;
        $this->data->fee  = TransactionFees::DELEGATE_RESIGNATION;
    }
}
