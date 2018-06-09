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

namespace ArkEcosystem\ArkCrypto\Transactions;

use ArkEcosystem\ArkCrypto\Crypto;
use ArkEcosystem\ArkCrypto\Enums\TransactionFees;

class SecondSignature extends Transaction
{
    /**
     * [__construct description].
     */
    public function __construct()
    {
        parent::__construct();

        $this->type   = TransactionTypes::SECOND_SIGNATURE;
        $this->fee    = TransactionFees::SECOND_SIGNATURE;
        $this->amount = 0;
    }

    /**
     * [secondSign description].
     *
     * @param string $secondSecret
     *
     * @return \ArkEcosystem\ArkCrypto\Transactions\Transaction
     */
    protected function secondSign(string $secondSecret): self
    {
        $this->asset['signature'] = [
            'publicKey' => Crypto::getKeys($secondSecret)->getPublicKey()->getHex(),
        ];

        Crypto::secondSign($this, Crypto::getKeys($secondSecret));

        return $this;
    }
}
