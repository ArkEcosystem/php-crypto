<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions;

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;
use ArkEcosystem\Crypto\Configuration\Network;
use ArkEcosystem\Crypto\Utils\Address;
use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PrivateKey;
use BitWasp\Bitcoin\Crypto\Hash;
use BitWasp\Buffertools\Buffer;

class Transaction
{
    public array $data;

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
     * Serialize the EVM call transaction data.
     *
     * @param array $options
     * @return ByteBuffer
     */
    public function serializeData(array $options = []): ByteBuffer
    {
        $buffer = ByteBuffer::new(0);

        // Write amount (uint256)
        $buffer->writeUint256($this->data['amount']);

        // Write recipient marker and recipientId (if present)
        if (isset($this->data['recipientId'])) {
            $buffer->writeUInt8(1); // Recipient marker
            $buffer->writeHex(
                Address::toBufferHexString($this->data['recipientId'])
            );
        } else {
            $buffer->writeUInt8(0); // No recipient
        }

        // Write gasLimit (uint32)
        $buffer->writeUInt32($this->data['asset']['evmCall']['gasLimit']);

        // Write payload length (uint32) and payload
        $payloadHex    = $this->data['asset']['evmCall']['payload'];
        $payloadLength = strlen($payloadHex);

        $buffer->writeUInt32($payloadLength / 2);

        // Write payload as hex
        $buffer->writeHex($payloadHex);

        return $buffer;
    }

    /**
     * Deserialize the EVM call transaction data.
     *
     * @param ByteBuffer $buffer
     */
    public function deserializeData(ByteBuffer $buffer): void
    {
        // Read amount (uint64)
        $this->data['amount'] = $buffer->readUInt256();

        // Read recipient marker and recipientId
        $recipientMarker = $buffer->readUInt8();
        if ($recipientMarker === 1) {
            $this->data['recipientId'] = Address::fromByteBuffer($buffer);
        }

        // Read gasLimit (uint32)
        $gasLimit = $buffer->readUInt32();

        // Read payload length (uint32)
        $payloadLength = $buffer->readUInt32();

        // Read payload as hex
        $payloadHex = $buffer->readHex($payloadLength * 2);

        $this->data['asset'] = [
            'evmCall' => [
                'gasLimit' => $gasLimit,
                'payload'  => $payloadHex,
            ],
        ];
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
        $scriptPath = __DIR__.'/../../scripts';

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
