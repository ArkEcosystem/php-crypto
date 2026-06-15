<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions\Types;

class Transfer extends AbstractTransaction
{
    public function getPayload(): string
    {
        return '';
    }
}
