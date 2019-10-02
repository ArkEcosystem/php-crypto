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

namespace ArkEcosystem\Crypto\Transactions\Builder;

use ArkEcosystem\Crypto\Transactions\Types\Transfer;

/**
 * This is the transfer transaction class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class TransferBuilder extends AbstractTransactionBuilder
{
    /**
     * Set the recipient of the transfer.
     *
     * @param string $recipientId
     *
     * @return \ArkEcosystem\Crypto\Transactions\Builder\Transfer
     */
    public function recipient(string $recipientId): self
    {
        $this->transaction->data['recipientId'] = $recipientId;

        return $this;
    }

    /**
     * Set the amount to transfer.
     *
     * @param int $amount
     *
     * @return \ArkEcosystem\Crypto\Transactions\Builder\Transfer
     */
    public function amount(int $amount): self
    {
        $this->transaction->data['amount'] = $amount;

        return $this;
    }

    /**
     * Set the vendor field / smartbridge.
     *
     * @param string $vendorField
     *
     * @return \ArkEcosystem\Crypto\Transactions\Builder\Transfer
     */
    public function vendorField(string $vendorField): self
    {
        $this->transaction->data['vendorField'] = $vendorField;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getType(): int
    {
        return \ArkEcosystem\Crypto\Enums\Types::TRANSFER;
    }

    protected function getTransactionInstance(): object
    {
        return new Transfer();
    }
}
