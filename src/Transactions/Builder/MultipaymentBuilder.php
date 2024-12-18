<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions\Builder;

use ArkEcosystem\Crypto\Enums\ContractAddresses;
use ArkEcosystem\Crypto\Transactions\Types\AbstractTransaction;
use ArkEcosystem\Crypto\Transactions\Types\Multipayment;

class MultipaymentBuilder extends AbstractTransactionBuilder
{
    public function __construct(?array $data = null)
    {
        parent::__construct($data);

        $this->recipientAddress(ContractAddresses::MULTIPAYMENT->value);

        $this->transaction->data['pay'] = [[], []];
        $this->transaction->refreshPayloadData();
    }

    public function pay(string $address, string $amount): self
    {
        // TODO: validate amount and address?
        $this->transaction->data['pay'][0][] = $address;
        $this->transaction->data['pay'][1][] = $amount;

        $this->transaction->refreshPayloadData();

        $this->transaction->data['value'] += $amount;

        return $this;
    }

    protected function getTransactionInstance(?array $data = []): AbstractTransaction
    {
        return new Multipayment($data);
    }
}
