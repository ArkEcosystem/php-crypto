<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Unit\Transactions;

use ArkEcosystem\Crypto\Transactions\Types\AbstractTransaction;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * @covers \ArkEcosystem\Crypto\Transactions\Deserializer
 */
class DeserializerTest extends TestCase
{
    /** @test */
    public function it_should_deserialize_a_transfer_signed_with_a_passphrase()
    {
        $fixture = $this->getTransactionFixture('evm_call', 'transfer');

        $transaction = $this->assertTransaction($fixture);

        expect($transaction->data['amount'])->toEqual('100000000');
    }

    /** @test */
    public function it_should_deserialize_a_vote_signed_with_a_passphrase()
    {
        $fixture = $this->getTransactionFixture('evm_call', 'vote');

        $transaction = $this->assertTransaction($fixture);

        expect($transaction->data['asset']['vote'])->toEqual('0x512F366D524157BcF734546eB29a6d687B762255');
    }

    /** @test */
    public function it_should_deserialize_a_unvote_signed_with_a_passphrase()
    {
        $fixture = $this->getTransactionFixture('evm_call', 'unvote');

        $transaction = $this->assertTransaction($fixture);

        expect($transaction->data['asset']['vote'])->toEqual('0x512F366D524157BcF734546eB29a6d687B762255');
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

    private function assertTransaction(array $fixture): AbstractTransaction
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
