<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions\Builder;

use ArkEcosystem\Crypto\Transactions\Types\ValidatorRegistration;

class ValidatorRegistrationBuilder extends AbstractTransactionBuilder
{
    /**
     * Create a new delegate registration transaction instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->transaction->data['asset'] = [];
    }

    /**
     * Set the username to assign.
     *
     * @param string $username
     *
     * @return self
     */
    public function publicKeyAsset(string $publicKey): self
    {
        if ($publicKey) {
            $this->transaction->data['asset']['validatorPublicKey'] = $publicKey;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getType(): int
    {
        return \ArkEcosystem\Crypto\Enums\Types::VALIDATOR_REGISTRATION->value;
    }

    protected function getTypeGroup(): int
    {
        return \ArkEcosystem\Crypto\Enums\TypeGroup::CORE;
    }

    protected function getTransactionInstance(): object
    {
        return new ValidatorRegistration();
    }
}
