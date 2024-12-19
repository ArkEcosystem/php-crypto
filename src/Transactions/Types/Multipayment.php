<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions\Types;

use ArkEcosystem\Crypto\Enums\AbiFunction;
use ArkEcosystem\Crypto\Enums\ContractAbiType;
use ArkEcosystem\Crypto\Utils\AbiEncoder;

class Multipayment extends AbstractTransaction
{
    public function __construct(?array $data = [])
    {
        $payload = $this->decodePayload($data, ContractAbiType::MULTIPAYMENT);

        // TODO: test this approach
        if ($payload !== null) {
            $data['pay'] = $payload['args'][0];
        }

        parent::__construct($data);
    }

    public function getPayload(): string
    {
        if (! array_key_exists('pay', $this->data)) {
            return '';
        }

        return (new AbiEncoder(ContractAbiType::MULTIPAYMENT))->encodeFunctionCall(AbiFunction::MULTIPAYMENT->value, $this->data['pay']);
    }
}
