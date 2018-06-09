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

use ArkEcosystem\ArkCrypto\Enums\TransactionFees;
use ArkEcosystem\ArkCrypto\Enums\TransactionTypes;

class Transfer extends Transaction
{
    /**
     * [__construct description].
     */
    public function __construct()
    {
        parent::__construct();

        $this->type = TransactionTypes::TRANSFER;
        $this->fee  = TransactionFees::TRANSFER;
    }

    /**
     * [withRecipientId description].
     *
     * @param string $recipientId
     *
     * @return \ArkEcosystem\ArkCrypto\Transactions\Transaction
     */
    public function withRecipientId(string $recipientId): void
    {
        $this->recipientId = $recipientId;

        return $this;
    }

    /**
     * [withAmount description].
     *
     * @param int $amount
     *
     * @return \ArkEcosystem\ArkCrypto\Transactions\Transaction
     */
    public function withAmount(int $amount): void
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * [withVendorField description].
     *
     * @param string $vendorField
     *
     * @return \ArkEcosystem\ArkCrypto\Transactions\Transaction
     */
    public function withVendorField(string $vendorField): void
    {
        $this->vendorField = $vendorField;

        return $this;
    }
}
