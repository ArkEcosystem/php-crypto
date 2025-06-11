<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions\Types;

use ArkEcosystem\Crypto\Enums\AbiFunction;
use ArkEcosystem\Crypto\Enums\ContractAbiType;
use ArkEcosystem\Crypto\Transactions\Deserializer;
use ArkEcosystem\Crypto\Utils\AbiEncoder;

class UsernameRegistration extends AbstractTransaction
{
    public function __construct(array $data)
    {
        $payload = Deserializer::decodePayload($data, ContractAbiType::USERNAMES);

        if ($payload !== null) {
            $data['username'] = $payload['args'][0];
        }

        parent::__construct($data);
    }

    public function getPayload(): string
    {
        if (! array_key_exists('username', $this->data)) {
            return '';
        }

        return (new AbiEncoder(ContractAbiType::USERNAMES))->encodeFunctionCall(AbiFunction::USERNAME_REGISTRATION->value, [$this->data['username']]);
    }
}
