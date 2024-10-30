<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Unit\Transactions\Serializers;

use ArkEcosystem\Crypto\Transactions\Types\Transfer;
use ArkEcosystem\Crypto\Transactions\Types\Unvote;
use ArkEcosystem\Crypto\Transactions\Types\ValidatorRegistration;
use ArkEcosystem\Crypto\Transactions\Types\ValidatorResignation;
use ArkEcosystem\Crypto\Transactions\Types\Vote;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * @covers \ArkEcosystem\Crypto\Transactions\Serializer
 */
class SerializerTest extends TestCase
{
    /** @test */
    public function it_should_serialize_a_transfer_transaction()
    {
        $fixture = $this->getTransactionFixture('evm_call', 'transfer');

        $transaction = new Transfer($fixture['data']);

        $this->assertSame($fixture['serialized'], $transaction->serialize()->getHex());
    }

    /** @test */
    public function it_should_serialize_a_vote_transaction()
    {
        $fixture = $this->getTransactionFixture('evm_call', 'vote');

        $transaction = new Vote($fixture['data']);

        $this->assertSame($fixture['serialized'], $transaction->serialize()->getHex());
    }

    /** @test */
    public function it_should_serialize_a_unvote_transaction()
    {
        $fixture = $this->getTransactionFixture('evm_call', 'unvote');

        $transaction = new Unvote($fixture['data']);

        $this->assertSame($fixture['serialized'], $transaction->serialize()->getHex());
    }

    /** @test */
    public function it_should_serialize_a_validator_registration_transaction()
    {
        $fixture = $this->getTransactionFixture('evm_call', 'validator-registration');

        $transaction = new ValidatorRegistration($fixture['data']);

        $this->assertSame($fixture['serialized'], $transaction->serialize()->getHex());
    }

    /** @test */
    public function it_should_serialize_a_validator_resignation_transaction()
    {
        $fixture = $this->getTransactionFixture('evm_call', 'validator-resignation');

        $transaction = new ValidatorResignation($fixture['data']);

        $this->assertSame($fixture['serialized'], $transaction->serialize()->getHex());
    }
}
