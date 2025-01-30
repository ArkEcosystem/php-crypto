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

        expect($transaction)->toBeInstanceOf(Transfer::class);
    }

    /** @test */
    public function it_should_deserialize_a_transfer_signed_with_a_passphrase_with_0_value()
    {
        $fixture = $this->getTransactionFixture('evm_call', 'transfer-0');

        $transaction = $this->assertTransaction($fixture);

        expect($transaction)->toBeInstanceOf(Transfer::class);
    }

    /** @test */
    public function it_should_deserialize_a_vote_signed_with_a_passphrase()
    {
        $fixture = $this->getTransactionFixture('evm_call', 'vote');

        $transaction = $this->assertTransaction($fixture);

        expect($transaction->data['vote'])->toEqual('0xC3bBE9B1CeE1ff85Ad72b87414B0E9B7F2366763');

        expect($transaction->data['id'])->toEqual('991a3a63dc47be84d7982acb4c2aae488191373f31b8097e07d3ad95c0997e69');

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
            'network',
            'nonce',
            'value',
            'gasPrice',
            'gasLimit',
            'contractId',
            'senderPublicKey',
            'senderAddress',
            'recipientAddress',
            'v',
            'r',
            's',
        ]);

        $this->assertTrue($actual->verify());

        return $actual;
    }
}
