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

namespace ArkEcosystem\Tests\Crypto\Deserialisers;

use ArkEcosystem\Crypto\Deserialisers\Transfer;
use ArkEcosystem\Crypto\Models\Transaction;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * This is the transfer deserialiser test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @coversNothing
 */
class TransferTest extends TestCase
{
    /** @test */
    public function it_should_deserialise_the_transaction()
    {
        $transaction = $this->getTransactionFixture(0);

        $actual = (new Transfer($transaction))->deserialise();

        $this->assertSame($transaction->version, $actual->version);
        $this->assertSame($transaction->network, $actual->network);
        $this->assertSame($transaction->type, $actual->type);
        $this->assertSame($transaction->senderPublicKey, $actual->senderPublicKey);
        $this->assertSame($transaction->serialized, Transaction::fromObject($actual)->serialise()->getHex());
    }
}
