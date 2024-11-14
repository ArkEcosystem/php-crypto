<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Unit\Transactions;

use ArkEcosystem\Crypto\Transactions\Types\AbstractTransaction;
use ArkEcosystem\Crypto\Transactions\Types\Transfer;
use ArkEcosystem\Crypto\Transactions\Types\Unvote;
use ArkEcosystem\Crypto\Transactions\Types\ValidatorRegistration;
use ArkEcosystem\Crypto\Transactions\Types\ValidatorResignation;
use ArkEcosystem\Crypto\Transactions\Types\Vote;
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

        expect($transaction->data['value'])->toEqual('10000000000000000000');

        expect($transaction)->toBeInstanceOf(Transfer::class);
    }

    /** @test */
    public function it_should_deserialize_a_vote_signed_with_a_passphrase()
    {
        $fixture = $this->getTransactionFixture('evm_call', 'vote');

        $transaction = $this->assertTransaction($fixture);

        expect($transaction->data['vote'])->toEqual('0x512F366D524157BcF734546eB29a6d687B762255');

        expect($transaction->data['id'])->toEqual('749744e0d689c46e37ff2993a984599eac4989a9ef0028337b335c9d43abf936');

        expect($transaction)->toBeInstanceOf(Vote::class);
    }

    /** @test */
    public function it_should_deserialize_a_unvote_signed_with_a_passphrase()
    {
        $fixture = $this->getTransactionFixture('evm_call', 'unvote');

        $transaction = $this->assertTransaction($fixture);

        expect($transaction)->toBeInstanceOf(Unvote::class);
    }

    /** @test */
    public function it_should_deserialize_a_validator_registration_signed_with_a_passphrase()
    {
        $fixture = $this->getTransactionFixture('evm_call', 'validator-registration');

        $transaction = $this->assertTransaction($fixture);

        expect($transaction)->toBeInstanceOf(ValidatorRegistration::class);
    }

    /** @test */
    public function it_should_deserialize_a_validator_resignation_signed_with_a_passphrase()
    {
        $fixture = $this->getTransactionFixture('evm_call', 'validator-resignation');

        $transaction = $this->assertTransaction($fixture);

        expect($transaction)->toBeInstanceOf(ValidatorResignation::class);
    }

    private function assertTransaction(array $fixture): AbstractTransaction
    {
        $actual = $this->assertDeserialized($fixture, [
            'id',
            'nonce',
            'gasPrice',
            'gasLimit',
            'contractId',
            'signature',
        ]);

        $this->assertTrue($actual->verify());

        return $actual;
    }
}
