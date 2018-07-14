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
use ArkEcosystem\Crypto\Deserializers\Vote;
use ArkEcosystem\Crypto\Transaction;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * This is the vote deserializer test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @coversNothing
 */
class VoteTest extends TestCase
{
    /** @test */
    public function it_should_deserialize_the_transaction_signed_with_a_passphrase()
    {
        $fixture = $this->getTransactionFixture('vote', 'passphrase');

        $this->assertTransaction($fixture);
    }

    /** @test */
    public function it_should_deserialize_the_transaction_signed_with_a_second_passphrase()
    {
        $fixture = $this->getTransactionFixture('vote', 'second-passphrase');

        $actual = $this->assertTransaction($fixture);
        $this->assertSame($fixture['data']['signSignature'], $actual->signSignature);
    }

    private function assertTransaction(array $fixture): Transaction
    {
        return $this->assertDeserialized($fixture, [
            'type',
            'timestamp',
            'senderPublicKey',
            'fee',
            'asset',
            'signature',
            'amount',
            'recipientId',
            'id',
        ]);
    }
}
