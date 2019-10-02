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

use ArkEcosystem\Tests\Crypto\TestCase;
use ArkEcosystem\Crypto\Transactions\Serializer;
use ArkEcosystem\Crypto\Transactions\Types\Transfer;

/**
 * This is the transfer serializer test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @covers \ArkEcosystem\Crypto\Transactions\Types\Transfer
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
        $this->assertSerialized($this->getTransactionFixture('transfer', 'passphrase-with-vendor-field'));
    }

    /** @test */
    public function it_should_serialize_the_transaction_with_a_second_passphrase_and_vendor_field()
    {
        $this->assertSerialized($this->getTransactionFixture('transfer', 'second-passphrase-with-vendor-field'));
    }

    /** @test */
    public function it_should_serialize_the_transaction_with_a_passphrase_and_vendor_field_hex()
    {
        $this->assertSerialized($this->getTransactionFixture('transfer', 'passphrase-with-vendor-field-hex'));
    }

    /** @test */
    public function it_should_serialize_the_transaction_with_a_second_passphrase_and_vendor_field_hex()
    {
        $this->assertSerialized($this->getTransactionFixture('transfer', 'second-passphrase-with-vendor-field-hex'));
    }
}
