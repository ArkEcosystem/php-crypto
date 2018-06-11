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

namespace ArkEcosystem\Crypto\Transactions;

use ArkEcosystem\Crypto\Crypto;
use ArkEcosystem\Crypto\Enums\TransactionFees;
use ArkEcosystem\Crypto\Enums\TransactionTypes;

/**
 * This is the delegate registration transaction class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class DelegateRegistration extends Transaction
{
    /**
     * Create a new delegate registration transaction instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->data->type              = TransactionTypes::DELEGATE_REGISTRATION;
        $this->data->fee               = TransactionFees::DELEGATE_REGISTRATION;
        $this->data->amount            = 0;
        $this->data->asset['delegate'] = [];
    }

    /**
     * Set the username to assign.
     *
     * @param string $username
     *
     * @return \ArkEcosystem\Crypto\Transactions\DelegateRegistration
     */
    public function username(string $username): self
    {
        $this->data->asset['delegate']['username'] = $username;

        return $this;
    }

    /**
     * Sign the transaction using the given secret.
     *
     * @param string $secret
     *
     * @return \ArkEcosystem\Crypto\Transactions\Transaction
     */
    public function sign(string $secret): Transaction
    {
        $keys                          = Crypto::getKeys($secret);
        $this->data->senderPublicKey   = $keys->getPublicKey()->getHex();

        $this->data->asset['delegate']['publicKey'] = $this->data->senderPublicKey;

        Crypto::sign($this->getSignedObject(), $keys);

        return $this;
    }
}
