<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions\Types;

use ArkEcosystem\Crypto\Configuration\Network;
use ArkEcosystem\Crypto\Identities\Address;
use ArkEcosystem\Crypto\Transactions\Serializer;
use ArkEcosystem\Crypto\Utils\AbiDecoder;
use ArkEcosystem\Crypto\Utils\TransactionHasher;
use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PrivateKey;
use BitWasp\Bitcoin\Crypto\Hash;
use BitWasp\Bitcoin\Key\Factory\PublicKeyFactory;
use BitWasp\Bitcoin\Signature\SignatureFactory;
use BitWasp\Buffertools\Buffer;

abstract class AbstractTransaction
{
    public array $data;

    public function __construct(?array $data = null)
    {
        $this->data = $data ?? [];
    }

    abstract public function getPayload(): string;

    public function decodePayload(array $data): ?array
    {
        if (! isset($data['asset']['evmCall']['payload'])) {
            return null;
        }

        $payload = $data['asset']['evmCall']['payload'];

        if ($payload === '') {
            return null;
        }

        return (new AbiDecoder())->decodeFunctionData($payload);
    }

    /**
     * Convert the byte representation to a unique identifier.
     */
    public function getId(): string
    {
        return Hash::sha256(Serializer::getBytes($this))->getHex();
    }

    public function getBytes($options = []): Buffer
    {
        return Serializer::getBytes($this, $options);
    }

    /**
     * Sign the transaction using the given passphrase.
     */
    public function sign(PrivateKey $keys): static
    {
        $options = [
            'skipSignature'       => true,
            'skipSecondSignature' => true,
        ];

        $hash = TransactionHasher::toHash($this->getHashData(), $options);

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

    /**
     * Sign the transaction using the given second passphrase.
     */
    public function secondSign(PrivateKey $keys): static
    {
        $options = [
            'skipSecondSignature' => true,
        ];
        $transaction                   = Hash::sha256($this->getBytes($options));

        $this->data['secondSignature'] = $keys->sign($transaction)->getBuffer()->getHex();

        return $this;
    }

    /**
     * Sign the transaction using the given passphrase.
     */
    public function multiSign(PrivateKey $keys, int $index = -1): static
    {
        if (! isset($this->data['signatures'])) {
            $this->data['signatures'] = [];
        }

        $index = $index === -1 ? count($this->data['signatures']) : $index;

        $transactionHash             = Hash::sha256($this->getBytes([
            'skipSignature'       => true,
            'skipMultiSignature'  => true,
        ]));

        $signature = $keys->sign($transactionHash)->getBuffer()->getHex();

        $indexedSignature = $this->numberToHex($index).$signature;

        $this->data['signatures'][] = $indexedSignature;

        return $this;
    }

    public function verify(): bool
    {
        $options = [
            'skipSignature'             => true,
            'skipSecondSignature'       => true,
        ];

        $publicKey = $this->data['senderPublicKey'];
        $signature = $this->data['signature'];

        $transaction = Hash::sha256($this->getBytes($options));

        $factory   = new PublicKeyFactory();
        $publicKey = $factory->fromHex($publicKey);

        return $publicKey->verify(
            $transaction,
            SignatureFactory::fromHex($signature)
        );
    }

    public function secondVerify(string $secondPublicKey): bool
    {
        $options = [
            'skipSecondSignature' => true,
        ];

        $signature = $this->data['secondSignature'];

        $transaction = Hash::sha256($this->getBytes($options));

        $factory   = new PublicKeyFactory();
        $publicKey = $factory->fromHex($secondPublicKey);

        return $publicKey->verify(
            $transaction,
            SignatureFactory::fromHex($signature)
        );
    }

    public function serialize(array $options = []): Buffer
    {
        return Serializer::new($this)->serialize($options);
    }

    /**
     * Convert the transaction to its array representation.
     */
    public function toArray(): array
    {
        return array_filter([
            'fee'                  => $this->data['fee'],
            'id'                   => $this->data['id'],
            'network'              => $this->data['network'] ?? Network::get()->version(),
            'nonce'                => $this->data['nonce'],
            'senderPublicKey'      => $this->data['senderPublicKey'],
            'signature'            => $this->data['signature'],
            'type'                 => $this->data['type'],
            'typeGroup'            => $this->data['typeGroup'],
            'version'              => $this->data['version'] ?? 1,
            'signatures'           => $this->data['signatures'] ?? null,
            'recipientId'          => $this->data['recipientId'] ?? null,
            'amount'               => $this->data['amount'],
            'asset'                => $this->data['asset'],
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

    private function getHashData(): array
    {
        return [
            'gasPrice'         => $this->data['fee'],
            'network'          => $this->data['network'] ?? Network::get()->version(),
            'nonce'            => $this->data['nonce'],
            'value'            => $this->data['amount'],
            'senderAddress'    => Address::fromPublicKey($this->data['senderPublicKey']),
            'gasLimit'         => $this->data['asset']['evmCall']['gasLimit'],
            'data'             => $this->data['asset']['evmCall']['payload'],
            'recipientAddress' => $this->data['recipientId'] ?? null,
            'senderPublicKey'  => $this->data['senderPublicKey'],
        ];
    }

    private function numberToHex(int $number, $padding = 2): string
    {
        // Convert the number to hexadecimal
        $indexHex = dechex($number);

        // Pad the hexadecimal string with leading zeros
        return str_pad($indexHex, $padding, '0', STR_PAD_LEFT);
    }
}
