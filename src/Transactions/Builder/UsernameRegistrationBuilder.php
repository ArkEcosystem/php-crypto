<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions\Builder;

use ArkEcosystem\Crypto\Enums\ContractAddresses;
use ArkEcosystem\Crypto\Helpers;
use ArkEcosystem\Crypto\Transactions\Types\AbstractTransaction;
use ArkEcosystem\Crypto\Transactions\Types\UsernameRegistration;

class UsernameRegistrationBuilder extends AbstractTransactionBuilder
{
    public function __construct(?array $data = null)
    {
        parent::__construct($data);

        $this->recipientAddress(ContractAddresses::USERNAMES->value);
    }

    public function username(string $username): self
    {
        Helpers::isValidUsername($username);

        $this->transaction->data['username'] = $username;

        $this->transaction->refreshPayloadData();

        return $this;
    }

    protected function getTransactionInstance(array $data): AbstractTransaction
    {
        return new UsernameRegistration($data);
    }
}
