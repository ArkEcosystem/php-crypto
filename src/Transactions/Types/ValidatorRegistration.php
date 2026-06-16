<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions\Types;

use ArkEcosystem\Crypto\Enums\AbiFunction;
use ArkEcosystem\Crypto\Helpers;
use ArkEcosystem\Crypto\Transactions\Deserializer;
use ArkEcosystem\Crypto\Utils\AbiEncoder;

class ValidatorRegistration extends AbstractTransaction
{
    public function __construct(array $data)
    {
        $payload = Deserializer::decodePayload($data);

        if ($payload !== null) {
            $data['validatorPublicKey'] = Helpers::removeLeadingHexZero($payload['args'][0]);
            $data['validatorProof']     = Helpers::removeLeadingHexZero($payload['args'][1]);
        }

        parent::__construct($data);
    }

    public function getPayload(): string
    {
        if (! array_key_exists('validatorPublicKey', $this->data) || ! array_key_exists('validatorProof', $this->data)) {
            return '';
        }

        return (new AbiEncoder())->encodeFunctionCall(
            AbiFunction::VALIDATOR_REGISTRATION->value,
            ['0x'.$this->data['validatorPublicKey'], '0x'.$this->data['validatorProof']]
        );
    }
}
