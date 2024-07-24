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

namespace ArkEcosystem\Tests\Crypto\Unit\Transactions\Serializers;

use ArkEcosystem\Crypto\Identities\PrivateKey;
use ArkEcosystem\Crypto\Identities\PublicKey;
use ArkEcosystem\Crypto\Transactions\Deserializer;
use ArkEcosystem\Crypto\Transactions\Types\Transaction;
use ArkEcosystem\Crypto\Transactions\Types\Transfer;
use ArkEcosystem\Tests\Crypto\TestCase;
use BitWasp\Buffertools\Buffer;

/**
 * This is the transaction test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @covers \ArkEcosystem\Crypto\Transactions\Types\Transaction
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

    private function getTransaction($file = 'transfer-sign'): Transfer
    {
        $fixture = $this->getTransactionFixture('transfer', $file);

        return Deserializer::new($fixture['serialized'])->deserialize();
    }
}
