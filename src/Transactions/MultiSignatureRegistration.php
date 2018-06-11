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

use ArkEcosystem\Crypto\Enums\TransactionFees;
use ArkEcosystem\Crypto\Enums\TransactionTypes;

/**
 * This is the multisignature registration transaction class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class MultiSignatureRegistration extends Transaction
{
    /**
     * Create a new multi signature transaction instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->data->type                    = TransactionTypes::MULTI_SIGNATURE;
        $this->data->amount                  = 0;
        $this->data->asset['multisignature'] = [];
    }

    /**
     * [min description].
     *
     * @param int $min
     *
     * @return \ArkEcosystem\Crypto\Transactions\Transaction
     */
    public function min(int $min): self
    {
        $this->data->asset['multisignature']['min'] = $min;

        return $this;
    }

    /**
     * [lifetime description].
     *
     * @param int $lifetime
     *
     * @return \ArkEcosystem\Crypto\Transactions\Transaction
     */
    public function lifetime(int $lifetime): self
    {
        $this->data->asset['multisignature']['lifetime'] = $lifetime;

        return $this;
    }

    /**
     * [keysgroup description].
     *
     * @param array $keysgroup
     *
     * @return \ArkEcosystem\Crypto\Transactions\Transaction
     */
    public function keysgroup(array $keysgroup): self
    {
        $this->data->asset['multisignature']['keysgroup'] = $keysgroup;

        $this->fee = (count($keysgroup) + 1) * TransactionFees::MULTI_SIGNATURE;

        return $this;
    }
}
