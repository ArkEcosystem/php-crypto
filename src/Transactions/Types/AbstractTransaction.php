<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions\Types;

use ArkEcosystem\Crypto\Configuration\Network;
use ArkEcosystem\Crypto\Identities\Address;
use ArkEcosystem\Crypto\Transactions\Serializer;
use ArkEcosystem\Crypto\Utils\AbiDecoder;
use ArkEcosystem\Crypto\Utils\TransactionHasher;
use BitWasp\Bitcoin\Bitcoin;
use BitWasp\Bitcoin\Crypto\EcAdapter\EcAdapterFactory;
use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PrivateKey;
use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Serializer\Signature\CompactSignatureSerializer;
use BitWasp\Bitcoin\Crypto\EcAdapter\Key\PublicKeyInterface;
use BitWasp\Bitcoin\Crypto\EcAdapter\Signature\CompactSignatureInterface;
use BitWasp\Buffertools\Buffer;
use BitWasp\Buffertools\BufferInterface;

abstract class AbstractTransaction
{
    public array $data;

    public function __construct(?array $data = null)
    {
        $this->data = $data ?? [];

        $this->refreshPayloadData();
    }

    abstract public function getPayload(): string;

    public function decodePayload(array $data): ?array
    {
        if (! isset($data['data'])) {
            return null;
        }

        $payload = $data['data'];

        if ($payload === '') {
            return null;
        }

        return (new AbiDecoder())->decodeFunctionData($payload);
    }

    public function refreshPayloadData(): static
    {
        $this->data['data'] = ltrim($this->getPayload(), '0x');

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

        $signature = $keys->signCompact($hash);

        // Extract the recovery ID (an integer between 0 and 3) from the signature
        $recoveryId = $signature->getRecoveryId();

        // Get the full signature buffer, which includes the adjusted recovery ID at the start
        $signatureHexWithRecoveryId = $signature->getBuffer()->getHex();

        // Apparently, the compact signature returned by signCompact() includes an adjusted recovery ID
        // as the first byte of the signature buffer. This adjusted recovery ID is specific to the compact
        // signature format used by the library and is calculated by adding a constant (typically 27 or 31)
        // to the actual recovery ID. This adjustment is done internally by the library for its own purposes.

        // However, in our context, and to match the expected signature format (as per the JavaScript
        // implementation), we need the raw signature consisting of only the 'r' and 's' values.
        // Therefore, we remove the first byte (two hex characters) from the signature buffer to exclude the adjusted recovery ID.
        $signatureHex = substr($signatureHexWithRecoveryId, 2);

        // Append the unadjusted recovery ID at the end of the signature
        // The unadjusted recovery ID is appended to match the expected signature format
        // This aligns with how the JavaScript implementation handles the recovery ID
        $signatureHex .= str_pad(dechex($recoveryId), 2, '0', STR_PAD_LEFT);

        $this->data['signature'] = $signatureHex;

        return $this;
    }

    public function getPublicKey(CompactSignatureInterface $compactSignature): PublicKeyInterface
    {
        $ecAdapter = EcAdapterFactory::getPhpEcc(
            Bitcoin::getMath(),
            Bitcoin::getGenerator()
        );

        return $ecAdapter->recover($this->hash(skipSignature: true), $compactSignature);
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
            'gasPrice'                   => $this->data['gasPrice'],
            'network'                    => $this->data['network'] ?? Network::get()->version(),
            'id'                         => $this->data['id'],
            'gasLimit'                   => $this->data['gasLimit'],
            'nonce'                      => $this->data['nonce'],
            'senderPublicKey'            => $this->data['senderPublicKey'],
            'signature'                  => $this->data['signature'],
            'recipientAddress'           => $this->data['recipientAddress'] ?? null,
            'value'                      => $this->data['value'],
            'data'                       => $this->data['data'],
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

    public function hash(bool $skipSignature): BufferInterface
    {
        $hashData = [
            'gasPrice'         => $this->data['gasPrice'],
            'network'          => $this->data['network'] ?? Network::get()->version(),
            'nonce'            => $this->data['nonce'],
            'value'            => $this->data['value'],
            'gasLimit'         => $this->data['gasLimit'],
            'data'             => $this->data['data'],
            'recipientAddress' => $this->data['recipientAddress'] ?? null,
            'signature'        => $this->data['signature'] ?? null,
        ];

        return TransactionHasher::toHash($hashData, $skipSignature);
    }

    private function getSignature(): CompactSignatureInterface
    {
        $ecAdapter = EcAdapterFactory::getPhpEcc(
            Bitcoin::getMath(),
            Bitcoin::getGenerator()
        );

        $recoverId = intval(substr($this->data['signature'], -2));

        $signature = substr($this->data['signature'], 0, -2);

        $serializer = new CompactSignatureSerializer($ecAdapter);

        return $serializer->parse(Buffer::hex($this->numberToHex($recoverId + 27 + 4).$signature));
    }

    private function numberToHex(int $number, $padding = 2): string
    {
        // Convert the number to hexadecimal
        $indexHex = dechex($number);

        // Pad the hexadecimal string with leading zeros
        return str_pad($indexHex, $padding, '0', STR_PAD_LEFT);
    }
}
