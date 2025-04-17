<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions\Builder;

use ArkEcosystem\Crypto\Enums\ContractAddresses;
use ArkEcosystem\Crypto\Transactions\Types\AbstractTransaction;
use ArkEcosystem\Crypto\Transactions\Types\Multipayment;
use Brick\Math\BigDecimal;

class MultipaymentBuilder extends AbstractTransactionBuilder
{
    public function __construct(?array $data = null)
    {
        parent::__construct($data);

        $this->to(ContractAddresses::MULTIPAYMENT->value);

        $this->transaction->data['pay'] = [[], []];
        $this->transaction->refreshPayloadData();
    }

    public function pay(string $address, BigDecimal $value): self
    {
        $this->transaction->data['pay'][0][] = $address;
        $this->transaction->data['pay'][1][] = $value;

        $this->transaction->refreshPayloadData();

        $this->transaction->data['value'] = $this->transaction->data['value']->plus($value);

        return $this;
    }

    protected function getTransactionInstance(array $data): AbstractTransaction
    {
        return new Multipayment($data);
    }
}
