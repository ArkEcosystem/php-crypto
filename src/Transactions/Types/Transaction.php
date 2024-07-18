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

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;
use ArkEcosystem\Crypto\Configuration\Network;
use ArkEcosystem\Crypto\EcAdapter\Impl\PhpEcc\Key\PrivateKey;
use ArkEcosystem\Crypto\EcAdapter\Impl\PhpEcc\Signature\SchnorrSigner;
use ArkEcosystem\Crypto\EcAdapter\Impl\Secp256k1\Signature\SchnorrSignature;
use ArkEcosystem\Crypto\Identities\PrivateKey as IdentitiesPrivateKey;
use ArkEcosystem\Crypto\Transactions\Serializer;
use BitWasp\Bitcoin\Crypto\Hash;
use BitWasp\Bitcoin\Key\Factory\PublicKeyFactory;
use BitWasp\Buffertools\Buffer;

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

    public SchnorrSignature $signature;

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
     * @param PrivateKey $keys
     *
     * @return Transaction
     */
    public function sign(PrivateKey $keys): self
    {
        $options = [
            'skipSignature'       => true,
        ];
        $transaction             = Hash::sha256($this->getBytes($options));

        $this->signature         = $keys->signSchnorr($transaction);

        $this->data['signature'] = $this->signature->getBuffer()->getHex();

        return $this;
    }

    public function verify(): bool
    {
        $options = [
            'skipSignature'       => true,
        ];

        $bytes     = $this->getBytes($options);

        $factory   = new PublicKeyFactory();
        $publicKey = $factory->fromHex($this->data['senderPublicKey']);
        $signer    = new SchnorrSigner(IdentitiesPrivateKey::ecAdapter());

        return $signer->verify(
            Hash::sha256($bytes),
            $signer->getXOnlyPublicKey($publicKey),
            $this->signature
        );
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
