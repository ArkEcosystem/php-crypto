<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions\Types;

use ArkEcosystem\Crypto\Enums\AbiFunction;
use ArkEcosystem\Crypto\Utils\AbiEncoder;

class ValidatorRegistration extends AbstractTransaction
{
    public function __construct(?array $data = [])
    {
        $payload = $this->decodePayload($data);

        if ($payload !== null) {
            $data['validatorPublicKey'] = $payload['args'][0];
        }

        parent::__construct($data);
    }

    public function getPayload(): string
    {
        if (! array_key_exists('validatorPublicKey', $this->data)) {
            return '';
        }

        return (new AbiEncoder())->encodeFunctionCall(AbiFunction::VALIDATOR_REGISTRATION->value, [$this->data['validatorPublicKey']]);
    }
}
