<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions\Types;

use ArkEcosystem\Crypto\Utils\AbiEncoder;

class Unvote extends AbstractTransaction
{
    public function getPayload(): string
    {
        return (new AbiEncoder())->encodeFunctionCall('unvote');
    }
}
