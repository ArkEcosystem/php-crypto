<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions\Builder;

use ArkEcosystem\Crypto\Configuration\Network;
use ArkEcosystem\Crypto\Identities\PrivateKey;
use ArkEcosystem\Crypto\Transactions\Types\AbstractTransaction;
use Brick\Math\BigDecimal;

abstract class AbstractTransactionBuilder
{
    public AbstractTransaction $transaction;

    public function __construct(?array $data = null)
    {
        $this->transaction = $this->getTransactionInstance($data ?? [
            'value'           => BigDecimal::zero(),
            'senderPublicKey' => '',
            'gasPrice'        => '5',
            'nonce'           => '1',
            'network'         => Network::get()->chainId(),
            'gasLimit'        => 1_000_000,
            'data'            => '',
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

    public function gasLimit(BigDecimal $gasLimit): static
    {
        $this->transaction->data['gasLimit'] = $gasLimit;

        return $this;
    }

    public function recipientAddress(string $recipientAddress): static
    {
        $this->transaction->data['recipientAddress'] = $recipientAddress;

        return $this;
    }

    public function gasPrice(BigDecimal $gasPrice): static
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
        $privateKey = PrivateKey::fromPassphrase($passphrase);

        $this->transaction->data['senderPublicKey'] = $privateKey->publicKey;

        $this->transaction = $this->transaction->sign($privateKey);

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

    abstract protected function getTransactionInstance(array $data): AbstractTransaction;
}
