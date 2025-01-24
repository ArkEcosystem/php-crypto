<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions;

use ArkEcosystem\Crypto\Helpers;
use ArkEcosystem\Crypto\Utils\Address;
use ArkEcosystem\Crypto\Utils\AbiDecoder;
use ArkEcosystem\Crypto\Utils\RlpEncoder;
use ArkEcosystem\Crypto\Enums\AbiFunction;
use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;
use ArkEcosystem\Crypto\Transactions\Types\Vote;
use ArkEcosystem\Crypto\Transactions\Types\Unvote;
use ArkEcosystem\Crypto\Transactions\Types\EvmCall;
use ArkEcosystem\Crypto\Transactions\Types\Transfer;
use ArkEcosystem\Crypto\Transactions\Types\AbstractTransaction;
use ArkEcosystem\Crypto\Transactions\Types\ValidatorResignation;
use ArkEcosystem\Crypto\Transactions\Types\ValidatorRegistration;

class Deserializer
{
    public const SIGNATURE_SIZE = 64;

    public const RECOVERY_SIZE  = 1;

    private ByteBuffer $buffer;

    private string $encodedRlp;

    /**
     * Create a new deserializer instance.
     */
    public function __construct(string $serialized)
    {
        $this->buffer = strpos($serialized, "\0") === false
            ? ByteBuffer::fromHex($serialized)
            : ByteBuffer::fromBinary($serialized);

        $this->encodedRlp = '0x'.mb_substr($this->buffer->toString('hex'), 2);
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
        $decodedRlp = RlpEncoder::decode($this->encodedRlp);

        $data = [];

        $data['network']          = $this->parseNumber($decodedRlp[0]); // Convert network (uint8) from hex to decimal
        $data['nonce']            = $this->parseBigNumber($decodedRlp[1]); // Convert nonce (uint64) from hex to decimal string
        $data['gasPrice']         = $this->parseNumber($decodedRlp[3]); // Convert gasPrice (uint32) from hex to decimal
        $data['gasLimit']         = $this->parseNumber($decodedRlp[4]); // Convert gasLimit (uint32) from hex to decimal
        $data['recipientAddress'] = $this->parseAddress($decodedRlp[5]); // Convert gasLimit (uint32) from hex to decimal
        $data['value']            = $this->parseBigNumber($decodedRlp[6]); // Convert value (large number) from hex to decimal string
        $data['data']             = $this->parseHex($decodedRlp[7]);

        if (count($decodedRlp) === 12) {
            $data['v'] = $this->parseNumber($decodedRlp[9]) + 27;
            $data['r'] = $this->parseHex($decodedRlp[10]);
            $data['s'] = $this->parseHex($decodedRlp[11]);
        }

        $transaction = $this->guessTransactionFromData($data);

        $eip1559Prefix = "02"; // marker for Type 2 (EIP1559) transaction which is the standard nowadays
        
        $transaction->serialized = sprintf('%s%s', $eip1559Prefix, mb_substr($this->encodedRlp, 2));

        // @TODO: Implement this
        // $transaction->data['id'] = $transaction->hash(skipSignature: false)->getHex();

        return $transaction;
    }

    private function guessTransactionFromData(array $data): AbstractTransaction
    {
        if ($data['value'] !== '0') {
            return new Transfer($data);
        }

        $payloadData = $this->decodePayload($data);

        if ($payloadData === null) {
            return new Transfer($data);
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
   
    private function parseNumber(string $value): int
    {
        return intval($value, 16);
    }

    private function parseBigNumber(string $value): string
    {
        return gmp_strval(gmp_init($value, 16));
    }

    private function parseHex(string $value): string
    {
        return Helpers::removeLeadingHexZero($value);
    }

    private function parseAddress(string $value): string
    {
        return $value;
    }

    // private function deserializeSignatures(array &$data): void
    // {
    //     $data['signature'] = $this->buffer->readHex((self::SIGNATURE_SIZE + self::RECOVERY_SIZE) * 2);
    // }
}
