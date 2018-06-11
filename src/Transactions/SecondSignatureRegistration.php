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
 * This is the second signature registration transaction class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class SecondSignatureRegistration extends Transaction
{
    /**
     * Create a new second signature registration transaction instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->data->type   = TransactionTypes::SECOND_SIGNATURE_REGISTRATION;
        $this->data->fee    = TransactionFees::SECOND_SIGNATURE_REGISTRATION;
        $this->data->amount = 0;
    }

    /**
     * [signature description].
     *
     * @param string $secondSecret
     *
     * @return \ArkEcosystem\Crypto\Transactions\Transaction
     */
    public function signature(string $secondSecret): self
    {
        $this->data->asset['signature'] = [
            'publicKey' => Crypto::getKeys($secondSecret)->getPublicKey()->getHex(),
        ];

        return $this;
    }
}
