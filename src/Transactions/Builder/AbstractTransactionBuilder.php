<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions\Builder;

use ArkEcosystem\Crypto\Configuration\Network;
use ArkEcosystem\Crypto\Identities\PrivateKey;
use ArkEcosystem\Crypto\Transactions\Types\AbstractTransaction;

abstract class AbstractTransactionBuilder
{
    public AbstractTransaction $transaction;

    public function __construct(?array $data = null)
    {
        $this->transaction = $this->getTransactionInstance($data ?? [
            'value'             => '0',
            'senderPublicKey'   => '',
            'gasPrice'          => '5',
            'nonce'             => '1',
            'network'           => Network::get()->pubKeyHash(),
            'gasLimit'          => 1_000_000,
            'data'              => '',
        ]);
    }

    public function __toString(): string
    {
        return $this->toJson();
    }

    public static function new(?array $data = null): static
    {
        return new static($data);
    }

    public function gasLimit(int $gasLimit): static
    {
        $this->transaction->data['gasLimit'] = $gasLimit;

        return $this;
    }

    public function recipientAddress(string $recipientAddress): static
    {
        $this->transaction->data['recipientAddress'] = $recipientAddress;

        return $this;
    }

    public function gasPrice(int $gasPrice): static
    {
        $this->transaction->data['gasPrice'] = $gasPrice;

        return $this;
    }

    public function nonce(string $nonce): static
    {
        $this->transaction->data['nonce'] = $nonce;

        return $this;
    }

    public function network(int $network): static
    {
        $this->transaction->data['network'] = $network;

        return $this;
    }

    public function sign(string $passphrase): static
    {
        $keys                                       = PrivateKey::fromPassphrase($passphrase);

        $this->transaction->data['senderPublicKey'] = $keys->getPublicKey()->getHex();

        $this->transaction             = $this->transaction->sign($keys);

        $this->transaction->data['id'] = $this->transaction->getId();

        return $this;
    }

    public function verify(): bool
    {
        return $this->transaction->verify();
    }

    public function toArray(): array
    {
        return $this->transaction->toArray();
    }

    public function toJson(): string
    {
        return $this->transaction->toJson();
    }

    abstract protected function getTransactionInstance(?array $data = []): AbstractTransaction;
}
