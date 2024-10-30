<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions\Builder;

use ArkEcosystem\Crypto\Transactions\Types\AbstractTransaction;
use ArkEcosystem\Crypto\Transactions\Types\Unvote;

class UnvoteBuilder extends AbstractTransactionBuilder
{
    protected function getTransactionInstance(?array $data = []): AbstractTransaction
    {
        return new Unvote($data);
    }
}
