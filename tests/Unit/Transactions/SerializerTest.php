<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Unit\Transactions\Serializers;

use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * @covers \ArkEcosystem\Crypto\Transactions\Serializer
 */
class SerializerTest extends TestCase
{
    /** @test */
    public function it_should_serialize_the_transaction_with_a_passphrase()
    {
        $this->assertSerialized($this->getTransactionFixture('evm_call', 'evm-sign'));
    }

    /** @test */
    public function it_should_serialize_a_transfer_transaction_with_a_passphrase()
    {
        $this->assertSerialized($this->getTransactionFixture('evm_call', 'evm-sign'));
    }

    /** @test */
    public function it_should_serialize_the_transaction_with_a_passphrase_and_contract_id()
    {
        $this->assertSerialized($this->getTransactionFixture('evm_call', 'evm-with-contract'));
    }
}
