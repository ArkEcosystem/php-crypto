<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions\Builder;

use ArkEcosystem\Crypto\Transactions\Types\AbstractTransaction;
use ArkEcosystem\Crypto\Transactions\Types\Transfer;

class TransferBuilder extends AbstractTransactionBuilder
{
    public function value(string $value): self
    {
        $this->transaction->data['value'] = $value;

        $this->transaction->refreshPayloadData();

        return $this;
    }

    protected function getTransactionInstance(?array $data = []): AbstractTransaction
    {
        return new Transfer($data);
    }
}
