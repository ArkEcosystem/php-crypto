<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions\Types;

use ArkEcosystem\Crypto\Enums\AbiFunction;
use ArkEcosystem\Crypto\Utils\AbiEncoder;

class Vote extends AbstractTransaction
{
    public function __construct(?array $data = [])
    {
        $payload = $this->decodePayload($data);

        if ($payload !== null) {
            $data['asset']['vote'] = $payload['args'][0];
        }

        parent::__construct($data);
    }

    public function getPayload(): string
    {
        return (new AbiEncoder())->encodeFunctionCall(AbiFunction::VOTE->value, [$this->data['asset']['vote']]);
    }
}
