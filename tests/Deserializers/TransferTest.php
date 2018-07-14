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

namespace ArkEcosystem\Tests\Crypto\Deserializers;

use ArkEcosystem\Crypto\Deserializer;
use ArkEcosystem\Crypto\Deserializers\Transfer;
use ArkEcosystem\Crypto\Transaction;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * This is the transfer deserializer test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @coversNothing
 */
class TransferTest extends TestCase
{
    /** @test */
    public function it_should_deserialize_the_transaction_signed_with_a_passphrase()
    {
        $fixture = $this->getTransactionFixture(0, 'passphrase');

        $actual = $this->assertTransaction($fixture);
        $this->assertSame(0, $actual->expiration);
    }

    /** @test */
    public function it_should_deserialize_the_transaction_signed_with_a_second_passphrase()
    {
        $fixture = $this->getTransactionFixture(0, 'second-passphrase');

        $actual = $this->assertTransaction($fixture);
        $this->assertSame($fixture['data']['signSignature'], $actual->signSignature);
    }

    /** @test */
    public function it_should_deserialize_the_transaction_signed_with_a_passphrase_and_vendor_field()
    {
        $fixture = $this->getTransactionFixture(0, 'passphrase-with-vendor-field');

        $actual = $this->assertTransaction($fixture);
        $this->assertSame($fixture['data']['vendorField'], $actual->vendorField);
    }

    /** @test */
    public function it_should_deserialize_the_transaction_signed_with_a_second_passphrase_and_vendor_field()
    {
        $fixture = $this->getTransactionFixture(0, 'second-passphrase-with-vendor-field');

        $actual = $this->assertTransaction($fixture);
        $this->assertSame($fixture['data']['vendorField'], $actual->vendorField);
    }

    /** @test */
    public function it_should_deserialize_the_transaction_signed_with_a_passphrase_and_vendor_field_hex()
    {
        $fixture = $this->getTransactionFixture(0, 'passphrase-with-vendor-field-hex');

        $actual = $this->assertTransaction($fixture);
        $this->assertSame(hex2bin($fixture['data']['vendorFieldHex']), $actual->vendorField);
    }

    /** @test */
    public function it_should_deserialize_the_transaction_signed_with_a_second_passphrase_and_vendor_field_hex()
    {
        $fixture = $this->getTransactionFixture(0, 'second-passphrase-with-vendor-field-hex');

        $actual = $this->assertTransaction($fixture);
        $this->assertSame(hex2bin($fixture['data']['vendorFieldHex']), $actual->vendorField);
    }

    private function assertTransaction(array $fixture): Transaction
    {
        return $this->assertDeserialized($fixture, [
            'type',
            'timestamp',
            'senderPublicKey',
            'fee',
            'amount',
            'recipientId',
            'signature',
            'id',
        ]);
    }
}
