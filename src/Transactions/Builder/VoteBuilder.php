<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions\Builder;

use ArkEcosystem\Crypto\Transactions\Types\AbstractTransaction;
use ArkEcosystem\Crypto\Transactions\Types\Vote;

class VoteBuilder extends AbstractTransactionBuilder
{
    public function vote(string $vote): self
    {
        $this->transaction->data['vote'] = $vote;

        $this->transaction->refreshPayloadData();

        return $this;
    }

    protected function getTransactionInstance(?array $data = []): AbstractTransaction
    {
        return new Vote($data);
    }
}
