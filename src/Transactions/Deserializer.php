<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions;

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;
use ArkEcosystem\Crypto\Enums\AbiFunction;
use ArkEcosystem\Crypto\Enums\Constants;
use ArkEcosystem\Crypto\Enums\ContractAbiType;
use ArkEcosystem\Crypto\Helpers;
use ArkEcosystem\Crypto\Transactions\Types\AbstractTransaction;
use ArkEcosystem\Crypto\Transactions\Types\EvmCall;
use ArkEcosystem\Crypto\Transactions\Types\Multipayment;
use ArkEcosystem\Crypto\Transactions\Types\Transfer;
use ArkEcosystem\Crypto\Transactions\Types\Unvote;
use ArkEcosystem\Crypto\Transactions\Types\UsernameRegistration;
use ArkEcosystem\Crypto\Transactions\Types\UsernameResignation;
use ArkEcosystem\Crypto\Transactions\Types\ValidatorRegistration;
use ArkEcosystem\Crypto\Transactions\Types\ValidatorResignation;
use ArkEcosystem\Crypto\Transactions\Types\Vote;
use ArkEcosystem\Crypto\Utils\AbiDecoder;
use ArkEcosystem\Crypto\Utils\RlpDecoder;
use BitWasp\Buffertools\Buffer;

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
        $decodedRlp = RlpDecoder::decode($this->encodedRlp);

        $data = [];

        $data['network']  = $this->parseNumber($decodedRlp[0]);
        $data['nonce']    = $this->parseBigNumber($decodedRlp[1]);
        $data['gasPrice'] = $this->parseNumber($decodedRlp[3]);
        $data['gas']      = $this->parseNumber($decodedRlp[4]);
        $data['to']       = $this->parseAddress($decodedRlp[5]);
        $data['value']    = $this->parseBigNumber($decodedRlp[6]);
        $data['data']     = $this->parseHex($decodedRlp[7]);

        if (count($decodedRlp) === 12) {
            $data['v'] = $this->parseNumber($decodedRlp[9]) + 27;
            $data['r'] = $this->parseHex($decodedRlp[10]);
            $data['s'] = $this->parseHex($decodedRlp[11]);
        }

        $transaction = $this->guessTransactionFromData($data);

        $serializedHex = sprintf('%s%s', Constants::EIP_1559_PREFIX, mb_substr($this->encodedRlp, 2));

        $transaction->serialized = new Buffer(hex2bin($serializedHex));

        $transaction->data['hash'] = $transaction->hash()->getHex();

        $transaction->recoverSender();

        return $transaction;
    }

    public static function decodePayload(array $data, ContractAbiType $abiType = ContractAbiType::CONSENSUS): ?array
    {
        if (! isset($data['data'])) {
            return null;
        }

        $payload = $data['data'];
        if ($payload === '') {
            return null;
        }

        $decoder = new AbiDecoder($abiType);

        try {
            return $decoder->decodeFunctionData($payload);
        } catch (\Throwable $e) {
            //
        }

        return null;
    }

    private function guessTransactionFromData(array $data): AbstractTransaction
    {
        $consensusPayloadData = $this->decodePayload($data);
        if ($consensusPayloadData !== null) {
            $functionName = null;
            if (array_key_exists('functionName', $consensusPayloadData)) {
                $functionName = $consensusPayloadData['functionName'];
            }

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
        }

        $usernamePayloadData = $this->decodePayload($data, ContractAbiType::USERNAMES);
        if ($usernamePayloadData !== null) {
            $functionName = null;
            if (array_key_exists('functionName', $usernamePayloadData)) {
                $functionName = $usernamePayloadData['functionName'];
            }

            if ($functionName === AbiFunction::USERNAME_REGISTRATION->value) {
                return new UsernameRegistration($data);
            }

            if ($functionName === AbiFunction::USERNAME_RESIGNATION->value) {
                return new UsernameResignation($data);
            }
        }

        $multipaymentPayloadData = $this->decodePayload($data, ContractAbiType::MULTIPAYMENT);
        if ($multipaymentPayloadData !== null) {
            $functionName = null;
            if (array_key_exists('functionName', $multipaymentPayloadData)) {
                $functionName = $multipaymentPayloadData['functionName'];
            }

            if ($functionName === AbiFunction::MULTIPAYMENT->value) {
                return new Multipayment($data);
            }
        }

        if ($data['value'] !== '0') {
            return new Transfer($data);
        }

        return new EvmCall($data);
    }

    private function parseNumber(string $value): int
    {
        return $value === '0x' ? 0 : intval($value, 16);
    }

    private function parseBigNumber(string $value): string
    {
        return $value === '0x' ? '0' : gmp_strval(gmp_init($value, 16));
    }

    private function parseHex(string $value): string
    {
        return Helpers::removeLeadingHexZero($value);
    }

    private function parseAddress(string $value): string|null
    {
        return $value === '0x' ? null : $value;
    }
}
