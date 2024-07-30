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
            'skipSecondSignature' => true,
        ];
        $transaction             = Hash::sha256($this->getBytes($options));

        // $this->data['signature'] = $keys->sign($transaction)->getBuffer()->getHex();
        $this->data['signature'] = $this->temporarySignerSign($transaction, $keys);

        return $this;
    }

    /**
     * Sign the transaction using the given second passphrase.
     *
     * @param PrivateKey $keys
     *
     * @return Transaction
     */
    public function secondSign(PrivateKey $keys): self
    {
        $options = [
            'skipSecondSignature' => true,
        ];
        $transaction                   = Hash::sha256($this->getBytes($options));

        $this->data['secondSignature'] = $this->temporarySignerSign($transaction, $keys);

        return $this;
    }

    /**
     * Sign the transaction using the given passphrase.
     *
     * @param PrivateKey $keys
     * @param int        $index
     *
     * @return Transaction
     */
    public function multiSign(PrivateKey $keys, int $index = -1): self
    {
        if (! isset($this->data['signatures'])) {
            $this->data['signatures'] = [];
        }

        $index = $index === -1 ? count($this->data['signatures']) : $index;

        $transactionHash             = Hash::sha256($this->getBytes([
            'skipSignature'       => true,
            'skipMultiSignature'  => true,
        ]));

        $signature = $this->temporarySignerSign($transactionHash, $keys);

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

        return $this->temporarySignerVerify($transaction, $signature, $publicKey);
    }

    public function secondVerify(string $secondPublicKey): bool
    {
        $options = [
            'skipSecondSignature' => true,
        ];

        $signature = $this->data['secondSignature'];

        $transaction = Hash::sha256($this->getBytes($options));

        return $this->temporarySignerVerify($transaction, $signature, $secondPublicKey);
    }

    public function serialize(array $options = []): Buffer
    {
        return Serializer::new($this)->serialize($options);
    }

    /**
     * Perform AIP11 compliant serialization.
     *
     * @return ByteBuffer $buffer
     */
    abstract public function serializeData(array $options = []): ByteBuffer;

    /**
     * Perform AIP11 compliant deserialization.
     *
     * @param ByteBuffer $buffer
     *
     * @return void
     */
    abstract public function deserializeData(ByteBuffer $buffer): void;

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

    private function numberToHex(int $number, $padding = 2): string
    {
        // Convert the number to hexadecimal
        $indexHex = dechex($number);

        // Pad the hexadecimal string with leading zeros
        return str_pad($indexHex, $padding, '0', STR_PAD_LEFT);
    }

    private function temporarySignerSign(Buffer $transaction, PrivateKey $keys)
    {
        $privateKey = $keys->getHex();

        $message    = $transaction->getHex();

        $command = "sign $privateKey $message";

        $result = $this->runTemporaryNodeCommand($command);

        return $result['signature'];
    }

    private function temporarySignerVerify(Buffer $transaction, string $signature, string $publicKey)
    {
        $message = $transaction->getHex();

        $command = "verify $publicKey $message $signature";

        $result = $this->runTemporaryNodeCommand($command);

        return $result['isValid'];
    }

    private function runTemporaryNodeCommand(string $command): array
    {
        $scriptPath = __DIR__.'/../../../scripts';

        $command = escapeshellcmd("npm start --prefix $scriptPath $command");

        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            $errorOutput = implode("\n", $output);

            throw new \RuntimeException("Error running signer script: $errorOutput");
        }

        $jsonOutput = implode("\n", $output);

        if (preg_match('/\{.*\}/s', $jsonOutput, $matches)) {
            $json = $matches[0];
        } else {
            throw new \RuntimeException("Error: Could not find JSON output in: $jsonOutput");
        }

        $result = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Error parsing JSON output: '.json_last_error_msg());
        }

        if ($result['status'] !== 'success') {
            throw new \RuntimeException('Error: '.$result['message']);
        }

        return $result;
    }
}
