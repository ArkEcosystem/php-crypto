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

use ArkEcosystem\ArkCrypto\Enums\TransactionFees;

class Vote extends Transaction
{
    /**
     * [__construct description].
     */
    public function __construct()
    {
        parent::__construct();

        $this->type   = TransactionTypes::VOTE;
        $this->fee    = TransactionFees::VOTE;
        $this->amount = 0;
    }

    /**
     * [withVotes description].
     *
     * @param array $votes
     *
     * @return \ArkEcosystem\ArkCrypto\Transactions\Transaction
     */
    public function withVotes(array $votes): self
    {
        $this->asset['votes'] = $votes;

        return $this;
    }

    /**
     * [sign description].
     *
     * @param string $secret
     *
     * @return \ArkEcosystem\ArkCrypto\Transactions\Transaction
     */
    protected function sign(string $secret): string
    {
        $this->recipientId = Crypto::getAddress(Crypto::getKeys($secret));

        parent::sign($this, $keys);

        return $this;
    }
}
