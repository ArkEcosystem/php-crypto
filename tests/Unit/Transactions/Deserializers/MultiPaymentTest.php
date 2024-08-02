<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Unit\Transactions\Deserializers;

use ArkEcosystem\Crypto\Transactions\Types\MultiPayment;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * @covers \ArkEcosystem\Crypto\Transactions\Types\MultiPayment
 */
class MultiPaymentTest extends TestCase
{
    /** @test */
    public function it_should_deserialize_the_transaction_signed_with_a_passphrase()
    {
        $transaction = $this->getTransactionFixture('multi_payment', 'multi-payment-sign');

        $this->assertTransaction($transaction);
    }

    private function assertTransaction(array $fixture): MultiPayment
    {
        $actual = $this->assertDeserialized($fixture, [
            'version',
            'network',
            'type',
            'typeGroup',
            'nonce',
            'senderPublicKey',
            'fee',
            'asset',
            'signature',
            'amount',
            'id',
        ]);

        $this->assertTrue($actual->verify());

        return $actual;
    }
}
