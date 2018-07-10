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

namespace ArkEcosystem\Tests\Crypto\Deserializers;

use ArkEcosystem\Crypto\Deserializer;
use ArkEcosystem\Crypto\Deserializers\Transfer;
use ArkEcosystem\Crypto\Serializer;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * This is the transfer deserializer test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @coversNothing
 */
class TransferTest extends TestCase
{
    /** @test */
    public function it_should_deserialize_the_transaction()
    {
        $transaction = $this->getTransactionFixture(0);

        $actual = Deserializer::new($transaction->serialized)->deserialize();

        $this->assertSame($transaction->version, $actual->version);
        $this->assertSame($transaction->network, $actual->network);
        $this->assertSame($transaction->type, $actual->type);
        $this->assertSame($transaction->timestamp, $actual->timestamp);
        $this->assertSame($transaction->senderPublicKey, $actual->senderPublicKey);
        $this->assertSame($transaction->fee, $actual->fee);
        $this->assertSame($transaction->amount, $actual->amount);
        $this->assertSame($transaction->expiration, $actual->expiration);
        $this->assertSame($transaction->recipientId, $actual->recipientId);
        $this->assertSame($transaction->signature, $actual->signature);
        $this->assertSame($transaction->id, $actual->id);
        $this->assertSame($transaction->serialized, Serializer::new($actual)->serialize()->getHex());
    }
}
