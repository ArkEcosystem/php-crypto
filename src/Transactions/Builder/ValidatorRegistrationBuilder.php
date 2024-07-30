<?php

declare(strict_types=1);

/*
 * This file is part of Ark PHP Crypto.
 *
 * (c) Ark Ecosystem <info@ark.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ArkEcosystem\Crypto\Transactions\Builder;

use ArkEcosystem\Crypto\Transactions\Types\ValidatorRegistration;

/**
 * This is the delegate registration transaction class.
 *
 * @author Brian Faust <brian@ark.io>
 */
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
