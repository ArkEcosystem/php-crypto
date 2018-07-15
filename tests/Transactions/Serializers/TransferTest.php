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

namespace ArkEcosystem\Tests\Crypto\Transactions\Serializers;

use ArkEcosystem\Crypto\Transactions\Serializer;
use ArkEcosystem\Crypto\Transactions\Serializers\Transfer;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * This is the transfer serializer test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @coversNothing
 */
class TransferTest extends TestCase
{
    /** @test */
    public function it_should_serialize_the_transaction_with_a_passphrase()
    {
        $this->assertSerialized($this->getTransactionFixture('transfer', 'passphrase'));
    }

    /** @test */
    public function it_should_serialize_the_transaction_with_a_second_passphrase()
    {
        $this->assertSerialized($this->getTransactionFixture('transfer', 'second-passphrase'));
    }

    /** @test */
    public function it_should_serialize_the_transaction_with_a_passphrase_and_vendor_field()
    {
        $transaction = $this->getTransactionFixture('transfer', 'passphrase-with-vendor-field');

        $actual = Serializer::new($transaction['data'])->serialize();

        $this->assertSame($transaction['serialized'], $actual->getHex());
    }

    /** @test */
    public function it_should_serialize_the_transaction_with_a_second_passphrase_and_vendor_field()
    {
        $transaction = $this->getTransactionFixture('transfer', 'second-passphrase-with-vendor-field');

        $actual = Serializer::new($transaction['data'])->serialize();

        $this->assertSame($transaction['serialized'], $actual->getHex());
    }

    /** @test */
    public function it_should_serialize_the_transaction_with_a_passphrase_and_vendor_field_hex()
    {
        $transaction = $this->getTransactionFixture('transfer', 'passphrase-with-vendor-field-hex');

        $actual = Serializer::new($transaction['data'])->serialize();

        $this->assertSame($transaction['serialized'], $actual->getHex());
    }

    /** @test */
    public function it_should_serialize_the_transaction_with_a_second_passphrase_and_vendor_field_hex()
    {
        $transaction = $this->getTransactionFixture('transfer', 'second-passphrase-with-vendor-field-hex');

        $actual = Serializer::new($transaction['data'])->serialize();

        $this->assertSame($transaction['serialized'], $actual->getHex());
    }
}
