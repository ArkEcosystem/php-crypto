<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions\Builder;

use ArkEcosystem\Crypto\Transactions\Types\MultiPayment;

class MultiPaymentBuilder extends AbstractTransactionBuilder
{
    /**
     * Create a new multi signature transaction instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->transaction->data['asset'] = ['payments' => []];
    }

    /**
     * Add a new payment to the collection.
     *
     * @param string $recipientId
     * @param string $amount
     *
     * @return self
     */
    public function add(string $recipientId, string $amount): self
    {
        $this->transaction->data['asset']['payments'][] = compact('recipientId', 'amount');

        $this->transaction->data['amount'] = strval(+$this->transaction->data['amount'] + +$amount);

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
        return \ArkEcosystem\Crypto\Enums\Types::MULTI_PAYMENT->value;
    }

    protected function getTypeGroup(): int
    {
        return \ArkEcosystem\Crypto\Enums\TypeGroup::CORE;
    }

    protected function getTransactionInstance(): object
    {
        return new MultiPayment();
    }
}
