<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Concerns;

use ArkEcosystem\Crypto\Transactions\Transaction;

trait Serialize
{
    protected function assertSerialized(array $fixture): void
    {
        $data              = $fixture['data'];
        $transaction       = new Transaction();
        $transaction->data = $data;

        $this->assertSame($fixture['serialized'], $transaction->serialize()->getHex());
    }
}
