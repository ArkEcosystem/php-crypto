<?php

declare(strict_types=1);

/*
 * This file is part of Ark PHP Crypto.
 *
 * (c) Ark Ecosystem <info@ark.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ArkEcosystem\Tests\Crypto\Concerns;

use ArkEcosystem\Crypto\Transactions\Serializer;
use ArkEcosystem\Crypto\Transactions\Types;

trait Serialize
{
    private $transactionsClasses = [
        Types\Transfer::class,
        Types\SecondSignatureRegistration::class,
        Types\DelegateRegistration::class,
        Types\Vote::class,
        Types\MultiSignatureRegistration::class,
        Types\IPFS::class,
        Types\MultiPayment::class,
        Types\DelegateResignation::class,
        Types\HtlcLock::class,
        Types\HtlcClaim::class,
        Types\HtlcRefund::class,
    ];

    protected function assertSerialized(array $fixture): void
    {
        $data              = $fixture['data'];
        $transactionClass  = $this->transactionsClasses[$fixture['data']['type']];
        $transaction       = new $transactionClass();
        $transaction->data = $data;

        $actual = Serializer::new($transaction)->serialize();

        $this->assertSame($fixture['serialized'], $actual->getHex());
    }
}
