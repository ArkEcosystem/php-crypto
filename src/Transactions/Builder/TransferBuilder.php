<?php

declare(strict_types=1);



namespace ArkEcosystem\Crypto\Transactions\Builder;

use ArkEcosystem\Crypto\Transactions\Types\Transfer;


class TransferBuilder extends AbstractTransactionBuilder
{
    public function __construct()
    {
        parent::__construct();

        $this->transaction->data['expiration'] = 0;
    }

    /**
     * Set the recipient of the transfer.
     *
     * @param string $recipientId
     *
     * @return self
     */
    public function recipient(string $recipientId): self
    {
        $this->transaction->data['recipientId'] = $recipientId;

        return $this;
    }

    /**
     * Set the amount to transfer.
     *
     * @param string $amount
     *
     * @return self
     */
    public function amount(string $amount): self
    {
        $this->transaction->data['amount'] = $amount;

        return $this;
    }

    /**
     * Set the vendor field / smartbridge.
     *
     * @param string $vendorField
     *
     * @return self
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
        return \ArkEcosystem\Crypto\Enums\Types::TRANSFER->value;
    }

    protected function getTypeGroup(): int
    {
        return \ArkEcosystem\Crypto\Enums\TypeGroup::CORE;
    }

    protected function getTransactionInstance(): object
    {
        return new Transfer();
    }
}
