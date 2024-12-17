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

        expect($transaction->data['vote'])->toEqual('0xC3bBE9B1CeE1ff85Ad72b87414B0E9B7F2366763');

        expect($transaction->data['id'])->toEqual('f5d593dd90a22301aa1f8418e6e208d9bd5bbe7806a30a52c3149f4b72338993');

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
