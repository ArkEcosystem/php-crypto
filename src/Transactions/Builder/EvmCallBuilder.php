<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions\Builder;

use ArkEcosystem\Crypto\Configuration\Fee;
use ArkEcosystem\Crypto\Configuration\Network;
use ArkEcosystem\Crypto\Enums\TypeGroup;
use ArkEcosystem\Crypto\Enums\Types;
use ArkEcosystem\Crypto\Identities\PrivateKey;
use ArkEcosystem\Crypto\Transactions\Transaction;

class EvmCallBuilder
{
    public Transaction $transaction;

    public function __construct()
    {
        $this->transaction                    = new Transaction();

        $this->transaction->data = [
            'type'            => Types::EVM_CALL->value,
            'typeGroup'       => TypeGroup::CORE,
            'amount'          => '0',
            'senderPublicKey' => '',
            'fee'             => Fee::get(Types::EVM_CALL->value),
            'nonce'           => '1',
            'version'         => 1,
            'network'         => Network::get()->pubKeyHash(),
            'asset'           => [
                'evmCall' => [
                    'gasLimit' => 1000000,  // Default gas limit
                    'payload'  => '',       // EVM code in hex format
                ],
            ],
        ];
    }

    public function __toString(): string
    {
        return $this->toJson();
    }

    public static function new(): self
    {
        return new static();
    }

    public function payload(string $payload): self
    {
        $payload                                                = ltrim($payload, '0x');
        $this->transaction->data['asset']['evmCall']['payload'] = $payload;

        return $this;
    }

    public function gasLimit(int $gasLimit): self
    {
        $this->transaction->data['asset']['evmCall']['gasLimit'] = $gasLimit;

        return $this;
    }

    public function recipient(string $recipientId): self
    {
        $this->transaction->data['recipientId'] = $recipientId;

        return $this;
    }

    public function amount(string $amount): self
    {
        $this->transaction->data['amount'] = $amount;

        return $this;
    }

    public function fee(string $fee): self
    {
        $this->transaction->data['fee'] = $fee;

        return $this;
    }

    /**
     * Alias for fee.
     */
    public function gasPrice(string $gasPrice): self
    {
        return $this->fee($gasPrice);
    }

    public function nonce(string $nonce): self
    {
        $this->transaction->data['nonce'] = $nonce;

        return $this;
    }

    public function network(int $network): self
    {
        $this->transaction->data['network'] = $network;

        return $this;
    }

    public function sign(string $passphrase): self
    {
        $keys                                       = PrivateKey::fromPassphrase($passphrase);
        $this->transaction->data['senderPublicKey'] = $keys->getPublicKey()->getHex();

        $this->transaction             = $this->transaction->sign($keys);
        $this->transaction->data['id'] = $this->transaction->getId();

        return $this;
    }

    public function multiSign(string $passphrase, int $index = -1): self
    {
        $keys              = PrivateKey::fromPassphrase($passphrase);
        $this->transaction = $this->transaction->multiSign($keys, $index);

        return $this;
    }

    public function secondSign(string $secondPassphrase): self
    {
        $this->transaction = $this->transaction->secondSign(
            PrivateKey::fromPassphrase($secondPassphrase)
        );
        $this->transaction->data['id'] = $this->transaction->getId();

        return $this;
    }

    public function verify(): bool
    {
        return $this->transaction->verify();
    }

    public function secondVerify(string $secondPublicKey): bool
    {
        return $this->transaction->secondVerify($secondPublicKey);
    }

    public function toArray(): array
    {
        return $this->transaction->toArray();
    }

    public function toJson(): string
    {
        return $this->transaction->toJson();
    }
}
