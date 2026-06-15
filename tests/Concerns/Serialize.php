<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Concerns;

use ArkEcosystem\Crypto\Transactions\Types\AbstractTransaction;

trait Serialize
{
    protected function assertSerialized(AbstractTransaction $transaction, array $fixture): void
    {
        $transaction->data = $fixture['data'];

        $this->assertSame($fixture['serialized'], $transaction->serialize()->getHex());
    }
}
