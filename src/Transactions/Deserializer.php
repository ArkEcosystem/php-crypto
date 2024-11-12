<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions;

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;
use ArkEcosystem\Crypto\Enums\AbiFunction;
use ArkEcosystem\Crypto\Transactions\Types\AbstractTransaction;
use ArkEcosystem\Crypto\Transactions\Types\EvmCall;
use ArkEcosystem\Crypto\Transactions\Types\Transfer;
use ArkEcosystem\Crypto\Transactions\Types\Unvote;
use ArkEcosystem\Crypto\Transactions\Types\ValidatorRegistration;
use ArkEcosystem\Crypto\Transactions\Types\ValidatorResignation;
use ArkEcosystem\Crypto\Transactions\Types\Vote;
use ArkEcosystem\Crypto\Utils\AbiDecoder;
use ArkEcosystem\Crypto\Utils\Address;

class Deserializer
{
    public const SIGNATURE_SIZE = 64;

    public const RECOVERY_SIZE  = 1;

    private ByteBuffer $buffer;

    /**
     * Create a new deserializer instance.
     */
    public function __construct(string $serialized)
    {
        $this->buffer = strpos($serialized, "\0") === false
            ? ByteBuffer::fromHex($serialized)
            : ByteBuffer::fromBinary($serialized);
    }

    /**
     * Create a new deserializer instance.
     */
    public static function new(string $serialized)
    {
        return new static($serialized);
    }

    /**
     * Perform AIP11 compliant deserialization.
     */
    public function deserialize(): AbstractTransaction
    {
        $data = [];

        $this->deserializeCommon($data);

        $this->deserializeData($data);

        $transaction = $this->guessTransactionFromData($data);

        $this->deserializeSignatures($transaction->data);

        $transaction->recoverSender();

        $transaction->data['id'] = $transaction->hash(skipSignature: false)->getHex();

        return $transaction;
    }

    private function guessTransactionFromData(array $data): AbstractTransaction
    {
        if ($data['value'] !== '0') {
            return new Transfer($data);
        }

        $payloadData = $this->decodePayload($data);

        if ($payloadData === null) {
            return new EvmCall();
        }

        $functionName = $payloadData['functionName'];

        if ($functionName === AbiFunction::VOTE->value) {
            return new Vote($data);
        }

        if ($functionName === AbiFunction::UNVOTE->value) {
            return new Unvote($data);
        }

        if ($functionName === AbiFunction::VALIDATOR_REGISTRATION->value) {
            return new ValidatorRegistration($data);
        }

        if ($functionName === AbiFunction::VALIDATOR_RESIGNATION->value) {
            return new ValidatorResignation($data);
        }

        return new EvmCall();
    }

    private function decodePayload(array $data): ?array
    {
        $payload = $data['data'];

        if ($payload === '') {
            return null;
        }

        return (new AbiDecoder())->decodeFunctionData($payload);
    }

    private function deserializeData(array &$data): void
    {
        // Read value (uint64)
        $data['value'] = $this->buffer->readUInt256();

        // Read recipient marker and recipientId
        $recipientMarker = $this->buffer->readUInt8();

        if ($recipientMarker === 1) {
            $data['recipientAddress'] = Address::fromByteBuffer($this->buffer);
        }

        // Read payload length (uint32)
        $payloadLength = $this->buffer->readUInt32();

        // Read payload as hex
        $payloadHex = $this->buffer->readHex($payloadLength * 2);

        $data['data'] = $payloadHex;
    }

    private function deserializeCommon(array &$data): void
    {
        $data['network']                   = $this->buffer->readUInt8();
        $data['nonce']                     = strval($this->buffer->readUInt64());
        $data['gasPrice']                  = $this->buffer->readUint32();
        $data['gasLimit']                  = $this->buffer->readUint32();
        $data['value']                     = '0';
    }

    private function deserializeSignatures(array &$data): void
    {
        $data['signature'] = $this->buffer->readHex((self::SIGNATURE_SIZE + self::RECOVERY_SIZE) * 2);
    }
}
