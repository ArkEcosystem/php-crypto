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
            'gas'             => 1_000_000,
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

    public function gas(BigDecimal $gas): static
    {
        $this->transaction->data['gas'] = $gas;

        return $this;
    }

    public function to(string $to): static
    {
        $this->transaction->data['to'] = $to;

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
        $keys                                       = PrivateKey::fromPassphrase($passphrase);

        $this->transaction->data['senderPublicKey'] = $keys->getPublicKey()->getHex();

        $this->transaction             = $this->transaction->sign($keys);

        $this->transaction->data['hash'] = $this->transaction->hash()->getHex();

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
