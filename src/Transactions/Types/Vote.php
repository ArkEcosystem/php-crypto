<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions\Types;

use ArkEcosystem\Crypto\Utils\AbiEncoder;

class Vote extends AbstractTransaction
{
    public function getPayload(): string
    {
        return (new AbiEncoder())->encodeFunctionCall('vote', [$this->data['asset']['vote']]);
    }
}
