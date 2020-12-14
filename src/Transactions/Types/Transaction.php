<?php

declare(strict_types=1);

/*
 * This file is part of Ark PHP Crypto.
 *
 * (c) Ark Ecosystem <info@ark.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ArkEcosystem\Crypto\Transactions\Types;

use ArkEcosystem\Crypto\Configuration\Network;
use ArkEcosystem\Crypto\Transactions\Serializer;
use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PrivateKey;
use BitWasp\Bitcoin\Crypto\Hash;
use BitWasp\Bitcoin\Key\Factory\PublicKeyFactory;
use BitWasp\Bitcoin\Signature\SignatureFactory;
use BitWasp\Buffertools\Buffer;
use Konceiver\ByteBuffer\ByteBuffer;

/**
 * This is the transaction class.
 *
 * @author Brian Faust <brian@ark.io>
 */
abstract class Transaction
{
    /**
     * @var object
     */
    public $data;

    /**
     * Convert the byte representation to a unique identifier.
     *
     * @return string
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
     *
     * @param \BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PrivateKey $keys
     *
     * @return Transaction
     */
    public function sign(PrivateKey $keys): self
    {
        $options = [
            'skipSignature'       => true,
            'skipSecondSignature' => true,
        ];
        $transaction             = Hash::sha256($this->getBytes($options));
        $this->data['signature'] = $keys->sign($transaction)->getBuffer()->getHex();

        return $this;
    }

    /**
     * Sign the transaction using the given second passphrase.
     *
     * @param \BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PrivateKey $keys
     *
     * @return Transaction
     */
    public function secondSign(PrivateKey $keys): self
    {
        $options = [
            'skipSecondSignature' => true,
        ];
        $transaction                   = Hash::sha256($this->getBytes($options));
        $this->data['secondSignature'] = $keys->sign($transaction)->getBuffer()->getHex();

        return $this;
    }

    public function verify(): bool
    {
        $options = [
            'skipSignature'       => true,
            'skipSecondSignature' => true,
        ];

        $bytes     = $this->getBytes($options);
        $publicKey = $this->data['senderPublicKey'];
        $signature = $this->data['signature'];

        return $this->verifySchnorrOrECDSA($bytes, $publicKey, $signature);
    }

    public function secondVerify(string $secondPublicKey): bool
    {
        $options = [
            'skipSecondSignature' => true,
        ];
        $bytes     = $this->getBytes($options);
        $signature = $this->data['secondSignature'];

        return $this->verifySchnorrOrECDSA($bytes, $secondPublicKey, $signature);
    }

    public function verifySchnorrOrECDSA(Buffer $bytes, string $publicKey, string $signature): bool
    {
        return $this->isSchnorr($signature)
            ? $this->verifySchnorr($bytes, $publicKey, $signature)
            : $this->verifyECDSA($bytes, $publicKey, $signature);
    }

    public function isSchnorr(string $signature): bool
    {
        return ByteBuffer::fromHex($signature)->capacity() === 64;
    }

    public function verifyECDSA(Buffer $bytes, string $publicKey, string $signature): bool
    {
        $factory   = new PublicKeyFactory();
        $publicKey = $factory->fromHex($publicKey);

        return $publicKey->verify(
            Hash::sha256($bytes),
            SignatureFactory::fromHex($signature)
        );
    }

    public function verifySchnorr(Buffer $bytes, string $publicKey, string $signature): bool
    {
        //TODO
        return false;
    }

    /**
     * Perform AIP11 compliant serialization.
     *
     * @return ByteBuffer $buffer
     */
    abstract public function serialize(array $options = []): ByteBuffer;

    /**
     * Perform AIP11 compliant deserialization.
     *
     * @param ByteBuffer $buffer
     *
     * @return void
     */
    abstract public function deserialize(ByteBuffer $buffer): void;

    /**
     * Convert the transaction to its array representation.
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_filter([
            'amount'               => $this->data['amount'],
            'asset'                => $this->data['asset'] ?? null,
            'fee'                  => $this->data['fee'],
            'id'                   => $this->data['id'],
            'network'              => $this->data['network'] ?? Network::get()->version(),
            'recipientId'          => $this->data['recipientId'] ?? null,
            'secondSignature'      => $this->data['secondSignature'] ?? null,
            'senderPublicKey'      => $this->data['senderPublicKey'],
            'signature'            => $this->data['signature'],
            'signatures'           => $this->data['signatures'] ?? null,
            'secondSignature'      => $this->data['secondSignature'] ?? null,
            'type'                 => $this->data['type'],
            'typeGroup'            => $this->data['typeGroup'],
            'nonce'                => $this->data['nonce'],
            'vendorField'          => $this->data['vendorField'] ?? null,
            'version'              => $this->data['version'] ?? 1,
        ], function ($element) {
            if (null !== $element) {
                return true;
            }

            return false;
        });
    }

    /**
     * Convert the transaction to its JSON representation.
     *
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    public function hasVendorField(): bool
    {
        return false;
    }
}
