<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Unit\Transactions;

use ArkEcosystem\Tests\Crypto\TestCase;
use ArkEcosystem\Crypto\Transactions\Transaction;

/**
 * @covers \ArkEcosystem\Crypto\Deserializer\Deserializer
 */
class DeserializerTest extends TestCase
{
    /** @test */
    public function it_should_deserialize_the_transfer_signed_with_a_passphrase()
    {
        $fixture = $this->getTransactionFixture('evm_call', 'transfer');

        $this->assertTransaction($fixture);
    }

    /** @test */
    public function it_should_deserialize_the_transaction_signed_with_a_passphrase()
    {
        $fixture = $this->getTransactionFixture('evm_call', 'evm-sign');

        $this->assertTransaction($fixture);
    }

    /** @test */
    public function it_should_deserialize_the_transaction_signed_with_a_contract()
    {
        $fixture = $this->getTransactionFixture('evm_call', 'evm-with-contract');

        $this->assertTransaction($fixture);
    }

    private function assertTransaction(array $fixture): Transaction
    {
        $actual = $this->assertDeserialized($fixture, [
            'nonce',
            'fee',
            'gasLimit',
            'contractId',
        ]);

        $this->assertTrue($actual->verify());

        return $actual;
    }
}
