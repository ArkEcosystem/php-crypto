<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions\Types;

use ArkEcosystem\Crypto\Enums\AbiFunction;
use ArkEcosystem\Crypto\Utils\AbiEncoder;

class ValidatorResignation extends AbstractTransaction
{
    public function getPayload(): string
    {
        return (new AbiEncoder())->encodeFunctionCall(AbiFunction::VALIDATOR_RESIGNATION->value);
    }
}
