<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions\Types;

class EvmCall extends AbstractTransaction
{
    public function getPayload(): string
    {
        return $this->data['asset']['evmCall']['payload'];
    }
}
