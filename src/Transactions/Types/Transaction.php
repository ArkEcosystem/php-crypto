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
use ArkEcosystem\Crypto\Transactions\Serializer;
use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PrivateKey;
use BitWasp\Bitcoin\Crypto\Hash;
use BitWasp\Bitcoin\Key\Factory\PublicKeyFactory;
use BitWasp\Bitcoin\Signature\SignatureFactory;
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
        // $this->data['signature'] = $keys->sign($transaction)->getBuffer()->getHex();
        $this->data['signature'] = $this->temporarySignerHack($transaction, $keys);

        return $this;
    }

    public function verify(): bool
    {
        $options = [
            'skipSignature'       => true,
        ];

        $bytes     = $this->getBytes($options);
        $publicKey = $this->data['senderPublicKey'];
        $signature = $this->data['signature'];

        return $this->verifySchnorrOrECDSA($bytes, $publicKey, $signature);
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
            'senderPublicKey'      => $this->data['senderPublicKey'],
            'signature'            => $this->data['signature'],
            'signatures'           => $this->data['signatures'] ?? null,
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

    private function temporarySignerHack(Buffer $transaction, PrivateKey $keys)
    {
        $publicKey  = $keys->getPublicKey()->getHex();
        $privateKey = $keys->getHex();
        $message    = $transaction->getHex();

        $scriptPath = __DIR__.'/../../../scripts';

        $command = escapeshellcmd("npm start --prefix $scriptPath $privateKey $publicKey $message");

        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            $errorOutput = implode("\n", $output);

            throw new \RuntimeException("Error running signer script: $errorOutput");
        }

        // Join the output lines
        $jsonOutput = implode("\n", $output);

        // Extract JSON part
        if (preg_match('/\{.*\}/s', $jsonOutput, $matches)) {
            $json = $matches[0];
        } else {
            throw new \RuntimeException("Error: Could not find JSON output in: $jsonOutput");
        }

        $result = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Error parsing JSON output: '.json_last_error_msg());
        }

        if ($result['status'] === 'success') {
            return $result['signature'];
        }

        throw new \RuntimeException('Error signing message: '.$result['message']);
    }
}
