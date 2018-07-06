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

namespace ArkEcosystem\Crypto\Builder;

/**
 * This is the transfer transaction class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class Transfer extends AbstractTransaction
{
    /**
     * Set the recipient of the transfer.
     *
     * @param string $recipientId
     *
     * @return \ArkEcosystem\Crypto\Builder\Transfer
     */
    public function recipient(string $recipientId): self
    {
        $this->transaction->recipientId = $recipientId;

        return $this;
    }

    /**
     * Set the amount to transfer.
     *
     * @param int $amount
     *
     * @return \ArkEcosystem\Crypto\Builder\Transfer
     */
    public function amount(int $amount): self
    {
        $this->transaction->amount = $amount;

        return $this;
    }

    /**
     * Set the vendor field / smartbridge.
     *
     * @param string $vendorField
     *
     * @return \ArkEcosystem\Crypto\Builder\Transfer
     */
    public function vendorField(string $vendorField): self
    {
        $this->transaction->vendorField = $vendorField;

        return $this;
    }
}
