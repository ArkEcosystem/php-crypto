<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions\Types;

use ArkEcosystem\Crypto\Configuration\Network;
use ArkEcosystem\Crypto\Enums\ContractAbiType;
use ArkEcosystem\Crypto\Helpers;
use ArkEcosystem\Crypto\Identities\Address;
use ArkEcosystem\Crypto\Identities\PrivateKey;
use ArkEcosystem\Crypto\Identities\PublicKey;
use ArkEcosystem\Crypto\Transactions\Deserializer;
use ArkEcosystem\Crypto\Transactions\Serializer;
use ArkEcosystem\Crypto\Utils\TransactionUtils;
use BitWasp\Bitcoin\Bitcoin;
use BitWasp\Bitcoin\Crypto\EcAdapter\EcAdapterFactory;
use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Signature\CompactSignature;
use BitWasp\Bitcoin\Crypto\EcAdapter\Signature\CompactSignatureInterface;
use BitWasp\Buffertools\Buffer;
use BitWasp\Buffertools\BufferInterface;

abstract class AbstractTransaction
{
    public array $data;

    public Buffer $serialized;

    public function __construct(array $data)
    {
        $this->data = $data ?? [];

        $this->refreshPayloadData();
    }

    abstract public function getPayload(): string;

    public function refreshPayloadData(): static
    {
        $this->data['data'] = Helpers::removeLeadingHexZero($this->getPayload());

        return $this;
    }

    /**
     * Sign the transaction using the given passphrase.
     */
    public function sign(PrivateKey $privateKey): static
    {
        $hash = $this->hash(skipSignature: true);

        $signature = $privateKey->sign($hash);

        // Extract the recovery ID (an integer between 0 and 3) from the signature
        $recoveryId = $signature->getRecoveryId();

        $this->data['v'] = $recoveryId + 27;
        $this->data['r'] = Helpers::gmpToHex($signature->getR());
        $this->data['s'] = Helpers::gmpToHex($signature->getS());

        return $this;
    }

    public function recoverSender(): void
    {
        $compactSignature = $this->getSignature();

        $publicKey = $this->recoverPublicKey($compactSignature);

        $this->data['senderPublicKey'] = $publicKey->publicKey;

        $this->data['from'] = Address::fromPublicKey($this->data['senderPublicKey']);
    }

    public function verify(): bool
    {
        $compactSignature = $this->getSignature();

        $publicKey = $this->recoverPublicKey($compactSignature);

        return $publicKey->instance->verify($this->hash(skipSignature: true), $compactSignature);
    }

    public function hash(bool $skipSignature = false): BufferInterface
    {
        return TransactionUtils::toHash($this->data, $skipSignature);
    }

    public function serialize(bool $skipSignature = false): Buffer
    {
        return Serializer::new($this)->serialize($skipSignature);
    }

    /**
     * Convert the transaction to its array representation.
     */
    public function toArray(): array
    {
        return array_filter([
            'gasPrice'        => $this->data['gasPrice'],
            'network'         => $this->data['network'] ?? Network::get()->chainId(),
            'hash'            => $this->data['hash'],
            'gas'             => $this->data['gas'],
            'nonce'           => $this->data['nonce'],
            'senderPublicKey' => $this->data['senderPublicKey'],
            'to'              => $this->data['to'] ?? null,
            'value'           => $this->data['value'],
            'data'            => $this->data['data'],
            'r'               => $this->data['r'],
            's'               => $this->data['s'],
            'v'               => $this->data['v'],
        ], function ($element) {
            if (null !== $element) {
                return true;
            }

            return false;
        });
    }

    /**
     * Convert the transaction to its JSON representation.
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    protected function recoverPublicKey(CompactSignatureInterface $compactSignature): PublicKey
    {
        return PublicKey::recover($this->hash(skipSignature: true), $compactSignature);
    }

    private function getSignature(): CompactSignatureInterface
    {
        $ecAdapter = EcAdapterFactory::getPhpEcc(
            Bitcoin::getMath(),
            Bitcoin::getGenerator()
        );

        $recoverId = $this->data['v'] - 27;
        $r         = gmp_init($this->data['r'], 16);
        $s         = gmp_init($this->data['s'], 16);

        return new CompactSignature(
            adapter: $ecAdapter,
            r: $r,
            s: $s,
            recid: $recoverId,
            compressed: true
        );
    }
}
