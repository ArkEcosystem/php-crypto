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

use ArkEcosystem\ArkCrypto\Enums\TransactionTypes;

class MultiSignature extends Transaction
{
    /**
     * [__construct description].
     */
    public function __construct()
    {
        parent::__construct();

        $this->type   = TransactionTypes::MULTI_SIGNATURE;
        $this->amount = 0;
    }

    /**
     * [withMinimum description].
     *
     * @param int $min
     *
     * @return \ArkEcosystem\ArkCrypto\Transactions\Transaction
     */
    public function withMinimum(int $min): self
    {
        $this->asset['multisignature']['min'] = $min;

        return $this;
    }

    /**
     * [withLifetime description].
     *
     * @param int $lifetime
     *
     * @return \ArkEcosystem\ArkCrypto\Transactions\Transaction
     */
    public function withLifetime(int $lifetime): self
    {
        $this->asset['multisignature']['lifetime'] = $lifetime;

        return $this;
    }

    /**
     * [withKeysgroup description].
     *
     * @param array $keysgroup
     *
     * @return \ArkEcosystem\ArkCrypto\Transactions\Transaction
     */
    public function withKeysgroup(array $keysgroup): self
    {
        $this->asset['multisignature']['keysgroup'] = $keysgroup;

        $this->fee = (count($keysgroup) + 1) * TransactionFees::MULTI_SIGNATURE;

        return $this;
    }
}
