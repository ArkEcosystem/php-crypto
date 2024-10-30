<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions\Builder;

use ArkEcosystem\Crypto\Transactions\Types\AbstractTransaction;
use ArkEcosystem\Crypto\Transactions\Types\ValidatorRegistration;

class ValidatorRegistrationBuilder extends AbstractTransactionBuilder
{
    protected function getTransactionInstance(): AbstractTransaction
    {
        return new ValidatorRegistration();
    }
}
