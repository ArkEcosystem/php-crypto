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

use ArkEcosystem\Crypto\Configuration\Fee;
use ArkEcosystem\Crypto\Identity\PrivateKey;
use ArkEcosystem\Crypto\Identity\PublicKey;
use ArkEcosystem\Crypto\Slot;
use ArkEcosystem\Crypto\Transactions\Transaction;

/**
 * This is the abstract transaction class.
 *
 * @author Brian Faust <brian@ark.io>
 */
abstract class AbstractTransaction
{
    /**
     * Create a new transaction instance.
     */
    public function __construct()
    {
        $this->transaction              = new Transaction();
        $this->transaction->type        = $this->getType();
        $this->transaction->amount      = 0;
        $this->transaction->fee         = $this->getFee();
        $this->transaction->timestamp   = Slot::getTime();
    }

    /**
     * Convert the message to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * Create a new transaction instance.
     *
     * @return \ArkEcosystem\Crypto\Transactions\Builder\AbstractTransaction
     */
    public static function new(): self
    {
        return new static();
    }

    /**
     * Set the transaction fee.
     *
     * @param int $fee
     *
     * @return \ArkEcosystem\Crypto\Transactions\Builder\AbstractTransaction
     */
    public function withFee(int $fee): self
    {
        $this->transaction->fee = $fee;

        return $this;
    }

    /**
     * Sign the transaction using the given passphrase.
     *
     * @param string $passphrase
     *
     * @return \ArkEcosystem\Crypto\Transactions\Builder\AbstractTransaction
     */
    public function sign(string $passphrase): self
    {
        $keys                               = PrivateKey::fromPassphrase($passphrase);
        $this->transaction->senderPublicKey = $keys->getPublicKey()->getHex();

        $this->transaction = $this->transaction->sign($keys);

        return $this;
    }

    /**
     * Sign the transaction using the given second passphrase.
     *
     * @param string $secondSecret
     *
     * @return \ArkEcosystem\Crypto\Transactions\Builder\AbstractTransaction
     */
    public function secondSign(string $secondSecret): self
    {
        $this->transaction = $this->transaction->secondSign(PrivateKey::fromPassphrase($secondSecret));

        return $this;
    }

    /**
     * Verify the transaction validity.
     *
     * @return bool
     */
    public function verify(): bool
    {
        return $this->transaction->verify();
    }

    /**
     * Verify the transaction validity with a second signature.
     *
     * @return bool
     */
    public function secondVerify(string $secondSecret): bool
    {
        return $this->transaction->secondVerify(PublicKey::fromPassphrase($secondSecret)->getHex());
    }

    /**
     * Convert the message to its plain object representation.
     *
     * @return \ArkEcosystem\Crypto\Transaction
     */
    public function getSignedObject(): Transaction
    {
        $this->transaction->id = $this->transaction->getId();

        if (empty($this->transaction->signSignature)) {
            unset($this->transaction->signSignature);
        }

        if (empty($this->transaction->asset)) {
            unset($this->transaction->asset);
        }

        return $this->transaction;
    }

    /**
     * Convert the message to its JSON representation.
     *
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->transaction);
    }

    /**
     * Get the transaction type.
     *
     * @return int
     */
    abstract protected function getType(): int;

    /**
     * Get the transaction fee.
     *
     * @return int
     */
    private function getFee(): int
    {
        return Fee::get($this->transaction->type);
    }
}
