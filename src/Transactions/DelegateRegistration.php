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
use ArkEcosystem\ArkCrypto\Enums\TransactionTypes;

class DelegateRegistration extends Transaction
{
    /**
     * [__construct description].
     */
    public function __construct()
    {
        parent::__construct();

        $this->type   = TransactionTypes::DELEGATE;
        $this->fee    = TransactionFees::DELEGATE;
        $this->amount = 0;
    }

    /**
     * [withDelegate description].
     *
     * @param string $username
     *
     * @return \ArkEcosystem\ArkCrypto\Transactions\Transaction
     */
    public function withDelegate(string $username): self
    {
        $this->asset['delegate'] = compact('username');

        return $this;
    }

    /**
     * Sign transaction using passphrase.
     *
     * @param string $secret
     *
     * @return \ArkEcosystem\ArkCrypto\Transactions\Transaction
     */
    protected function sign(string $secret): self
    {
        $keys                  = Crypto::getKeys($secret);
        $this->senderPublicKey = $keys->getPublicKey()->getHex();

        $this->asset['delegate']['publicKey'] = $this->senderPublicKey;

        Crypto::sign($this, $keys);

        return $this;
    }
}
