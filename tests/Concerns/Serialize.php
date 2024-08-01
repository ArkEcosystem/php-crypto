<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Concerns;

use ArkEcosystem\Crypto\Enums\Types;

trait Serialize
{
    protected function assertSerialized(array $fixture): void
    {
        $data              = $fixture['data'];
        $transactionClass  = Types::fromValue($fixture['data']['type'])->transactionClass();
        $transaction       = new $transactionClass();
        $transaction->data = $data;

        $this->assertSame($fixture['serialized'], $transaction->serialize()->getHex());
    }
}
