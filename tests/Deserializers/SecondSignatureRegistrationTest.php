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
use ArkEcosystem\Crypto\Identity\Address;
use ArkEcosystem\Crypto\Transaction;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * This is the second signature registration deserializer test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @coversNothing
 */
class SecondSignatureRegistrationTest extends TestCase
{
    /** @test */
    public function it_should_deserialize_the_transaction_signed_with_a_second_passphrase()
    {
        $transaction = $this->getTransactionFixture(1, 'second-passphrase');

        $actual = $this->assertTransaction($transaction);

        // special case as the type 1 transaction itself has no recipientId
        $this->assertSame($actual->recipientId, Address::fromPublicKey($actual->senderPublicKey, $actual->network));
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
            'id',
        ]);
    }
}
