<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions\Builder;

use ArkEcosystem\Crypto\Transactions\Types\AbstractTransaction;
use ArkEcosystem\Crypto\Transactions\Types\EvmCall;

class EvmCallBuilder extends AbstractTransactionBuilder
{
    public function payload(string $payload): self
    {
        $payload                                                = ltrim($payload, '0x');
        $this->transaction->data['asset']['evmCall']['payload'] = $payload;

        return $this;
    }

    protected function getTransactionInstance(?array $data = []): AbstractTransaction
    {
        return new EvmCall($data);
    }
}
