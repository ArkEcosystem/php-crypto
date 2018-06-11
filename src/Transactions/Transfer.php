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

use ArkEcosystem\Crypto\Enums\TransactionFees;
use ArkEcosystem\Crypto\Enums\TransactionTypes;

/**
 * This is the transfer transaction class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class Transfer extends Transaction
{
    /**
     * Create a new vote transaction instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->data->type = TransactionTypes::TRANSFER;
        $this->data->fee  = TransactionFees::TRANSFER;
    }

    /**
     * [withRecipientId description].
     *
     * @param string $recipientId
     *
     * @return \ArkEcosystem\Crypto\Transactions\Transaction
     */
    public function recipientID(string $recipientId): self
    {
        $this->data->recipientId = $recipientId;

        return $this;
    }

    /**
     * [withAmount description].
     *
     * @param int $amount
     *
     * @return \ArkEcosystem\Crypto\Transactions\Transaction
     */
    public function amount(int $amount): self
    {
        $this->data->amount = $amount;

        return $this;
    }

    /**
     * [withVendorField description].
     *
     * @param string $vendorField
     *
     * @return \ArkEcosystem\Crypto\Transactions\Transaction
     */
    public function vendorField(string $vendorField): self
    {
        $this->data->vendorField = $vendorField;

        return $this;
    }
}
