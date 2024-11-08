<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions\Builder;

use ArkEcosystem\Crypto\Transactions\Types\AbstractTransaction;
use ArkEcosystem\Crypto\Transactions\Types\ValidatorRegistration;

class ValidatorRegistrationBuilder extends AbstractTransactionBuilder
{
    public function validatorPublicKey(string $validatorPublicKey): self
    {
        $this->transaction->data['validatorPublicKey'] = $validatorPublicKey;

        return $this;
    }

    protected function getTransactionInstance(?array $data = []): AbstractTransaction
    {
        return new ValidatorRegistration($data);
    }
}
