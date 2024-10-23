<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions\Builder;

use ArkEcosystem\Crypto\Enums\Types;
use ArkEcosystem\Crypto\Enums\TypeGroup;
use ArkEcosystem\Crypto\Configuration\Fee;
use ArkEcosystem\Crypto\Transactions\Types\EvmCall;
use ArkEcosystem\Crypto\Configuration\Network;
use ArkEcosystem\Crypto\Identities\PrivateKey;

class EvmCallBuilder
{
    private EvmCall $transaction;

    /**
     * Create a new EVM call transaction instance.
     */
    public function __construct()
    {
        $this->transaction = new EvmCall();
        $this->transaction->data['type']     = Types::EVM_CALL->value;
        $this->transaction->data['typeGroup']     = TypeGroup::CORE;
        $this->transaction->data['nonce']     = '0';
        $this->transaction->data['amount']    = '0';
        $this->transaction->data['fee']       = $this->getFee();
        $this->transaction->data['version']   = 1;
        $this->transaction->data['network']   = Network::get()->pubKeyHash();
        $this->transaction->data['asset'] = [
            'evmCall' => [
                'gasLimit' => 1000000,  // Default gas limit
                'payload'  => '',       // EVM code in hex format
            ],
        ];
    }

    /**
     * Set the payload for the EVM call.
     *
     * @param string $payload
     * @return self
     */
    public function payload(string $payload): self
    {
        $payload                                                = ltrim($payload, '0x');
        $this->transaction->data['asset']['evmCall']['payload'] = $payload;

        return $this;
    }

    /**
     * Set the gas limit for the EVM call.
     *
     * @param int $gasLimit
     * @return self
     */
    public function withGasLimit(int $gasLimit): self
    {
        $this->transaction->data['asset']['evmCall']['gasLimit'] = $gasLimit;

        return $this;
    }

    /**
     * Set the recipient of the EVM call.
     *
     * @param string $recipientId
     * @return self
     */
    public function recipient(string $recipientId): self
    {
        $this->transaction->data['recipientId'] = $recipientId;

        return $this;
    }

    /**
     * Set the amount to transfer.
     *
     * @param string $amount
     *
     * @return self
     */
    public function amount(string $amount): self
    {
        $this->transaction->data['amount'] = $amount;

        return $this;
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
     * @return AbstractTransactionBuilder
     */
    public static function new(): self
    {
        return new static();
    }

    /**
     * Set the transaction fee.
     *
     * @param string $fee
     *
     * @return AbstractTransactionBuilder
     */
    public function withFee(string $fee): self
    {
        $this->transaction->data['fee'] = $fee;

        return $this;
    }

    /**
     * Set the transaction nonce.
     *
     * @param string $nonce
     *
     * @return AbstractTransactionBuilder
     */
    public function withNonce(string $nonce): self
    {
        $this->transaction->data['nonce'] = $nonce;

        return $this;
    }

    /**
     * Set the transaction network.
     *
     * @param int $network
     *
     * @return AbstractTransactionBuilder
     */
    public function withNetwork(int $network): self
    {
        $this->transaction->data['network'] = $network;

        return $this;
    }

    /**
     * Sign the transaction using the given passphrase.
     *
     * @param string $passphrase
     *
     * @return AbstractTransactionBuilder
     */
    public function sign(string $passphrase): self
    {
        $keys                                       = PrivateKey::fromPassphrase($passphrase);
        $this->transaction->data['senderPublicKey'] = $keys->getPublicKey()->getHex();

        $this->transaction             = $this->transaction->sign($keys);
        $this->transaction->data['id'] = $this->transaction->getId();

        return $this;
    }

    /**
     * Sign the transaction using the given passphrase.
     *
     * @param string $passphrase
     *
     * @return AbstractTransactionBuilder
     */
    public function multiSign(string $passphrase, int $index = -1): self
    {
        $keys = PrivateKey::fromPassphrase($passphrase);

        $this->transaction = $this->transaction->multiSign($keys, $index);

        return $this;
    }

    /**
     * Sign the transaction using the given second passphrase.
     *
     * @param string $secondPassphrase
     *
     * @return AbstractTransactionBuilder
     */
    public function secondSign(string $secondPassphrase): self
    {
        $this->transaction             = $this->transaction->secondSign(PrivateKey::fromPassphrase($secondPassphrase));
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
     * Get the transaction fee.
     *
     * @return string
     */
    protected function getFee(): string
    {
        return Fee::get($this->transaction->data['type']);
    }

}
