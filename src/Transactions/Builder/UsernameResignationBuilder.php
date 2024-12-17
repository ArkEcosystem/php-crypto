<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions\Builder;

use ArkEcosystem\Crypto\Enums\ContractAddresses;
use ArkEcosystem\Crypto\Transactions\Types\AbstractTransaction;
use ArkEcosystem\Crypto\Transactions\Types\UsernameResignation;

class UsernameResignationBuilder extends AbstractTransactionBuilder
{
    public function __construct(?array $data = null)
    {
        parent::__construct($data);

        $this->recipientAddress(ContractAddresses::USERNAMES->value);
    }

    protected function getTransactionInstance(?array $data = []): AbstractTransaction
    {
        return new UsernameResignation($data);
    }
}
