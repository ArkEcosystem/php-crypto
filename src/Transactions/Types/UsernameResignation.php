<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions\Types;

use ArkEcosystem\Crypto\Enums\AbiFunction;
use ArkEcosystem\Crypto\Enums\ContractAbiType;
use ArkEcosystem\Crypto\Utils\AbiEncoder;

class UsernameResignation extends AbstractTransaction
{
    public function getPayload(): string
    {
        return (new AbiEncoder(ContractAbiType::USERNAMES))->encodeFunctionCall(AbiFunction::USERNAME_RESIGNATION->value);
    }
}
