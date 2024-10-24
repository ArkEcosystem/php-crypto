<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions\Builder;

use ArkEcosystem\Crypto\Configuration\Fee;
use ArkEcosystem\Crypto\Configuration\Network;
use ArkEcosystem\Crypto\Enums\TypeGroup;
use ArkEcosystem\Crypto\Enums\Types;
use ArkEcosystem\Crypto\Identities\PrivateKey;
use ArkEcosystem\Crypto\Transactions\Types\EvmCall;

class EvmCallBuilder
{
    public EvmCall $transaction;

    public function __construct()
    {
        $this->transaction                    = new EvmCall();
        $this->transaction->data['type']      = Types::EVM_CALL->value;
        $this->transaction->data['typeGroup'] = TypeGroup::CORE;
        $this->transaction->data['nonce']     = '0';
        $this->transaction->data['amount']    = '0';
        $this->transaction->data['fee']       = $this->getFee();
        $this->transaction->data['version']   = 1;
        $this->transaction->data['network']   = Network::get()->pubKeyHash();
        $this->transaction->data['asset']     = [
            'evmCall' => [
                'gasLimit' => 1000000,  // Default gas limit
                'payload'  => '',       // EVM code in hex format
            ],
        ];
    }

    public static function new(): self
    {
        return new static();
    }

    public function payload(string $payload): self
    {
        $payload = ltrim($payload, '0x');
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
        $keys = PrivateKey::fromPassphrase($passphrase);
        $this->transaction->data['senderPublicKey'] = $keys->getPublicKey()->getHex();

        $this->transaction             = $this->transaction->sign($keys);
        $this->transaction->data['id'] = $this->transaction->getId();

        return $this;
    }

    public function multiSign(string $passphrase, int $index = -1): self
    {
        $keys = PrivateKey::fromPassphrase($passphrase);
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

    public function __toString(): string
    {
        return $this->toJson();
    }

    protected function getFee(): string
    {
        return Fee::get($this->transaction->data['type']);
    }
}
