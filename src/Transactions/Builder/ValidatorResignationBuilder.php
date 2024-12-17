<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions\Builder;

use ArkEcosystem\Crypto\Enums\ContractAddresses;
use ArkEcosystem\Crypto\Transactions\Types\AbstractTransaction;
use ArkEcosystem\Crypto\Transactions\Types\ValidatorResignation;

class ValidatorResignationBuilder extends AbstractTransactionBuilder
{
    public function __construct(?array $data = null)
    {
        parent::__construct($data);

        $this->recipientAddress(ContractAddresses::CONSENSUS->value);
    }

    protected function getTransactionInstance(?array $data = []): AbstractTransaction
    {
        return new ValidatorResignation($data);
    }
}
