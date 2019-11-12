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
use ArkEcosystem\Crypto\Identities\PrivateKey;
use ArkEcosystem\Crypto\Transactions\Types\Transaction;

/**
 * This is the abstract transaction class.
 *
 * @author Brian Faust <brian@ark.io>
 */
abstract class AbstractTransactionBuilder
{
    /**
     * Create a new transaction instance.
     */
    public function __construct()
    {
        $this->transaction = $this->getTransactionInstance();
        $this->transaction->data['type'] = $this->getType();
        $this->transaction->data['typeGroup'] = $this->getTypeGroup();
        $this->transaction->data['nonce'] = 0;
        $this->transaction->data['amount'] = 0;
        $this->transaction->data['fee'] = $this->getFee();
        $this->transaction->data['version'] = 2;
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
        $this->transaction->data['fee'] = $fee;

        return $this;
    }

    /**
     * Set the transaction nonce.
     *
     * @param int $nonce
     *
     * @return \ArkEcosystem\Crypto\Transactions\Builder\AbstractTransaction
     */
    public function withNonce(int $nonce): self
    {
        $this->transaction->data['nonce'] = $nonce;

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
        $keys = PrivateKey::fromPassphrase($passphrase);
        $this->transaction->data['senderPublicKey'] = $keys->getPublicKey()->getHex();

        $this->transaction = $this->transaction->sign($keys);
        $this->transaction->data['id'] = $this->transaction->getId();

        return $this;
    }

    /**
     * Sign the transaction using the given second passphrase.
     *
     * @param string $secondPassphrase
     *
     * @return \ArkEcosystem\Crypto\Transactions\Builder\AbstractTransaction
     */
    public function secondSign(string $secondPassphrase): self
    {
        $this->transaction = $this->transaction->secondSign(PrivateKey::fromPassphrase($secondPassphrase));
        $this->transaction->data['id'] = $this->transaction->getId();

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
    public function secondVerify(string $secondPublicKey): bool
    {
        return $this->transaction->secondVerify($secondPublicKey);
    }

    /**
     * Convert the transaction to its array representation.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->transaction->toArray();
    }

    /**
     * Convert the transaction to its JSON representation.
     *
     * @return string
     */
    public function toJson(): string
    {
        return $this->transaction->toJson();
    }

    /**
     * Get the transaction type.
     *
     * @return int
     */
    abstract protected function getType(): int;

    /**
     * Get the transaction typeGroup.
     *
     * @return int
     */
    abstract protected function getTypeGroup(): int;

    /**
     * Get the transaction instance.
     *
     * @return object
     */
    abstract protected function getTransactionInstance(): object;

    /**
     * Get the transaction fee.
     *
     * @return int
     */
    protected function getFee(): int
    {
        return Fee::get($this->transaction->data['type']);
    }
}
