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

use ArkEcosystem\Crypto\Transactions\Types\UsernameRegistration;

/**
 * This is the username registration transaction class.
 */
class UsernameRegistrationBuilder extends AbstractTransactionBuilder
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
    public function usernameAsset(string $username): self
    {
        if ($username) {
            $this->transaction->data['asset']['username'] = $username;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getType(): int
    {
        return \ArkEcosystem\Crypto\Enums\Types::USERNAME_REGISTRATION->value;
    }

    protected function getTypeGroup(): int
    {
        return \ArkEcosystem\Crypto\Enums\TypeGroup::CORE;
    }

    protected function getTransactionInstance(): object
    {
        return new UsernameRegistration();
    }
}
