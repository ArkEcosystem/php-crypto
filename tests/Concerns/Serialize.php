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
use ArkEcosystem\Crypto\Transactions\Types\IPFS;
use ArkEcosystem\Crypto\Transactions\Types\MultiPayment;
use ArkEcosystem\Crypto\Transactions\Types\MultiSignatureRegistration;
use ArkEcosystem\Crypto\Transactions\Types\SecondSignatureRegistration;
use ArkEcosystem\Crypto\Transactions\Types\Transfer;
use ArkEcosystem\Crypto\Transactions\Types\UsernameRegistration;
use ArkEcosystem\Crypto\Transactions\Types\UsernameResignation;
use ArkEcosystem\Crypto\Transactions\Types\ValidatorRegistration;
use ArkEcosystem\Crypto\Transactions\Types\ValidatorResignation;
use ArkEcosystem\Crypto\Transactions\Types\Vote;

trait Serialize
{
    private $transactionsClasses = [
        Transfer::class,
        SecondSignatureRegistration::class,
        ValidatorRegistration::class,
        Vote::class,
        MultiSignatureRegistration::class,
        IPFS::class,
        MultiPayment::class,
        ValidatorResignation::class,
        UsernameRegistration::class,
        UsernameResignation::class,
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
