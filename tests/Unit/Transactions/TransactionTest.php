<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Unit\Transactions\Serializers;

use ArkEcosystem\Crypto\Identities\PrivateKey;
use ArkEcosystem\Crypto\Transactions\Deserializer;
use ArkEcosystem\Crypto\Transactions\Transaction;
use ArkEcosystem\Tests\Crypto\TestCase;
use BitWasp\Buffertools\Buffer;

/**
 * @covers \ArkEcosystem\Crypto\Transactions\Transaction
 */
class TransactionTest extends TestCase
{
    /** @test */
    public function should_compute_the_id_of_the_transaction()
    {
        $actual = $this->getTransaction()->getId();

        $this->assertTrue(strlen($actual) === 64);
    }

    /** @test */
    public function should_sign_the_transaction_using_a_passphrase()
    {
        $privateKey = PrivateKey::fromPassphrase('this is a top secret passphrase');

        $transaction                    = $this->getTransaction();
        $transaction->data['signature'] = null;

        $this->assertEmpty($transaction->data['signature']);
        $transaction->sign($privateKey);
        $this->assertNotEmpty($transaction->data['signature']);
    }

    /** @test */
    public function should_verify_the_transaction()
    {
        $actual = $this->getTransaction()->verify();

        $this->assertTrue($actual);
    }

    /** @test */
    public function should_turn_the_transaction_to_bytes()
    {
        $actual = $this->getTransaction()->getBytes();

        $this->assertInstanceOf(Buffer::class, $actual);
    }

    /** @test */
    public function should_turn_the_transaction_to_an_array()
    {
        $actual = $this->getTransaction()->toArray();

        $this->assertIsArray($actual);
    }

    /** @test */
    public function should_turn_the_transaction_to_json()
    {
        $actual = $this->getTransaction()->toJson();

        $this->assertIsString($actual);
    }

    private function getTransaction($file = 'transfer'): Transaction
    {
        $fixture = $this->getTransactionFixture('evm_call', $file);

        return Deserializer::new($fixture['serialized'])->deserialize();
    }
}
