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
use ArkEcosystem\Crypto\Configuration\Network;
use ArkEcosystem\Crypto\Identities\PrivateKey;
use ArkEcosystem\Crypto\Transactions\Types\Transaction;

/**
 * This is the abstract transaction class.
 *
 * @author Brian Faust <brian@ark.io>
 */
abstract class AbstractTransactionBuilder
{
    public $transaction;

    /**
     * Create a new transaction instance.
     */
    public function __construct()
    {
        $this->transaction                    = $this->getTransactionInstance();
        $this->transaction->data['type']      = $this->getType();
        $this->transaction->data['typeGroup'] = $this->getTypeGroup();
        $this->transaction->data['nonce']     = '0';
        $this->transaction->data['amount']    = '0';
        $this->transaction->data['fee']       = $this->getFee();
        $this->transaction->data['version']   = 1;
        $this->transaction->data['network']   = Network::get()->pubKeyHash();
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
     */
    public static function new(): self
    {
        return new static();
    }

    /**
     * Set the transaction fee.
     */
    public function withFee(string $fee): self
    {
        $this->transaction->data['fee'] = $fee;

        return $this;
    }

    /**
     * Set the transaction nonce.
     */
    public function withNonce(string $nonce): self
    {
        $this->transaction->data['nonce'] = $nonce;

        return $this;
    }

    /**
     * Set the transaction network.
     */
    public function withNetwork(int $network): self
    {
        $this->transaction->data['network'] = $network;

        return $this;
    }

    /**
     * Sign the transaction using the given passphrase.
     */
    public function sign(string $passphrase): self
    {
        $keys = PrivateKey::fromPassphrase($passphrase);

        $this->transaction->data['senderPublicKey'] = $keys->getPublicKey()->getHex();

        $this->transaction             = $this->transaction->sign($keys);
        $this->transaction->data['id'] = $this->transaction->getId();

        return $this;
    }

    /**
     * Sign the transaction using the given second passphrase.
     */
    public function secondSign(string $secondPassphrase): self
    {
        $this->transaction             = $this->transaction->secondSign(PrivateKey::fromPassphrase($secondPassphrase));
        $this->transaction->data['id'] = $this->transaction->getId();

        return $this;
    }

    /**
     * Verify the transaction validity.
     */
    public function verify(): bool
    {
        return $this->transaction->verify();
    }

    /**
     * Verify the transaction validity with a second signature.
     */
    public function secondVerify(string $secondPublicKey): bool
    {
        return $this->transaction->secondVerify($secondPublicKey);
    }

    /**
     * Convert the transaction to its array representation.
     */
    public function toArray(): array
    {
        return $this->transaction->toArray();
    }

    /**
     * Convert the transaction to its JSON representation.
     */
    public function toJson(): string
    {
        return $this->transaction->toJson();
    }

    /**
     * Get the transaction type.
     */
    abstract protected function getType(): int;

    /**
     * Get the transaction typeGroup.
     */
    abstract protected function getTypeGroup(): int;

    /**
     * Get the transaction instance.
     */
    abstract protected function getTransactionInstance(): object;

    /**
     * Get the transaction fee.
     */
    protected function getFee(): string
    {
        return Fee::get($this->transaction->data['type']);
    }
}
