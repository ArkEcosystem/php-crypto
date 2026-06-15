<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions\Builder;

use ArkEcosystem\Crypto\Enums\ContractAddresses;
use ArkEcosystem\Crypto\Transactions\Types\AbstractTransaction;
use ArkEcosystem\Crypto\Transactions\Types\Unvote;

class UnvoteBuilder extends AbstractTransactionBuilder
{
    public function __construct(?array $data = null)
    {
        parent::__construct($data);

        $this->to(ContractAddresses::CONSENSUS->value);
    }

    protected function getTransactionInstance(array $data): AbstractTransaction
    {
        return new Unvote($data);
    }
}
