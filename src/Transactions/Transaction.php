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

abstract class Transaction
{
    public function __construct()
    {
        $this->id              = null;
        $this->timestamp       = $this->getTimeSinceEpoch();
        $this->type            = null;
        $this->amount          = null;
        $this->fee             = null;

        $this->senderPublicKey = null;
        $this->recipientId     = null;
        $this->asset           = [];

        $this->signature       = null;
        $this->signSignature   = null;
    }

    /**
     * [withFee description].
     *
     * @param int $fee
     *
     * @return \ArkEcosystem\ArkCrypto\Transactions\Transaction
     */
    public function withFee(int $fee): self
    {
        $this->fee = $fee;

        return $this;
    }

    /**
     * [getStruct description].
     *
     * @return \ArkEcosystem\ArkCrypto\Transactions\Transaction
     */
    protected function getStruct(): self
    {
        $idBytes  = Crypto::getBytes($this, false, false);
        $this->id = Hash::sha256(new Buffer($idBytes))->getHex();

        if (!$this->signSignature) {
            unset($this->signSignature);
        }

        unset($this->asset);

        return $this;
    }

    /**
     * [sign description].
     *
     * @param string $secret
     *
     * @return \ArkEcosystem\ArkCrypto\Transactions\Transaction
     */
    protected function sign(string $secret): self
    {
        $keys                  = Crypto::getKeys($secret);
        $this->senderPublicKey = $keys->getPublicKey()->getHex();

        Crypto::sign($this, $keys);

        return $this;
    }

    /**
     * Sign transaction using second passphrase.
     *
     * @param string $secondSecret
     *
     * @return \ArkEcosystem\ArkCrypto\Transactions\Transaction
     */
    protected function secondSign(string $secondSecret): self
    {
        Crypto::secondSign($this, Crypto::getKeys($secondSecret));

        return $this;
    }

    /**
     * [getTimeSinceEpoch description].
     *
     * @return string
     */
    protected function getTimeSinceEpoch(): string
    {
        return time() - strtotime('2017-03-21 13:00:00');
    }
}
