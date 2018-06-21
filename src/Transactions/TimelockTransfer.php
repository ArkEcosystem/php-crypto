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

use ArkEcosystem\Crypto\Transactions\Enums\Types;

/**
 * This is the timelock transfer transaction class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class TimelockTransfer extends Transaction
{
    /**
     * Create a new timelock transfer transaction instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->data->type = Types::TIMELOCK_TRANSFER;
        $this->data->fee  = Fees::TIMELOCK_TRANSFER;
    }
}
