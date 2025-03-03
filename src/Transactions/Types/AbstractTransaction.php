<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions\Types;

use ArkEcosystem\Crypto\Configuration\Network;
use ArkEcosystem\Crypto\Enums\ContractAbiType;
use ArkEcosystem\Crypto\Helpers;
use ArkEcosystem\Crypto\Identities\Address;
use ArkEcosystem\Crypto\Transactions\Deserializer;
use ArkEcosystem\Crypto\Transactions\Serializer;
use ArkEcosystem\Crypto\Utils\TransactionUtils;
use BitWasp\Bitcoin\Bitcoin;
use BitWasp\Bitcoin\Crypto\EcAdapter\EcAdapterFactory;
use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PrivateKey;
use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Signature\CompactSignature;
use BitWasp\Bitcoin\Crypto\EcAdapter\Key\PublicKeyInterface;
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
     * Convert the byte representation to a unique identifier.
     */
    public function getId(): string
    {
        return $this->hash(skipSignature: false)->getHex();
    }

    public function getBytes(bool $skipSignature = false): Buffer
    {
        return Serializer::getBytes($this, $skipSignature);
    }

    /**
     * Sign the transaction using the given passphrase.
     */
    public function sign(PrivateKey $keys): static
    {
        $hash = $this->hash(skipSignature: true);

        /** @var CompactSignature $signature */
        $signature = $keys->signCompact($hash);

        // Extract the recovery ID (an integer between 0 and 3) from the signature
        $recoveryId = $signature->getRecoveryId();

        $this->data['v'] = $recoveryId + 27;
        $this->data['r'] = $this->gmpToHex($signature->getR());
        $this->data['s'] = $this->gmpToHex($signature->getS());

        return $this;
    }

    public function recoverSender(): void
    {
        $compactSignature = $this->getSignature();

        $publicKey = $this->getPublicKey($compactSignature);

        $this->data['senderPublicKey'] = $publicKey->getHex();

        $this->data['senderAddress'] = Address::fromPublicKey($this->data['senderPublicKey']);
    }

    public function verify(): bool
    {
        $compactSignature = $this->getSignature();

        $publicKey = $this->getPublicKey($compactSignature);

        return $publicKey->verify($this->hash(skipSignature: true), $compactSignature);
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
            'gasPrice'         => $this->data['gasPrice'],
            'network'          => $this->data['network'] ?? Network::get()->chainId(),
            'id'               => $this->data['id'],
            'gasLimit'         => $this->data['gasLimit'],
            'nonce'            => $this->data['nonce'],
            'senderPublicKey'  => $this->data['senderPublicKey'],
            'recipientAddress' => $this->data['recipientAddress'] ?? null,
            'value'            => $this->data['value'],
            'data'             => $this->data['data'],
            'r'                => $this->data['r'],
            's'                => $this->data['s'],
            'v'                => $this->data['v'],
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

    protected function getPublicKey(CompactSignatureInterface $compactSignature): PublicKeyInterface
    {
        $ecAdapter = EcAdapterFactory::getPhpEcc(
            Bitcoin::getMath(),
            Bitcoin::getGenerator()
        );

        return $ecAdapter->recover($this->hash(skipSignature: true), $compactSignature);
    }

    protected function decodePayload(array $data, ContractAbiType $type = ContractAbiType::CONSENSUS): ?array
    {
        if (! isset($data['data'])) {
            return null;
        }

        return Deserializer::decodePayload($data, $type);
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

    private function gmpToHex(\GMP $gmp): string
    {
        $hex = gmp_strval($gmp, 16);

        return str_pad($hex, 64, '0', STR_PAD_LEFT);
    }
}
