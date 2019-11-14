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

namespace ArkEcosystem\Tests\Crypto\Transactions\Deserializers;

use ArkEcosystem\Crypto\Transactions\Deserializer;
use ArkEcosystem\Crypto\Transactions\Types\Transfer;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * This is the transfer deserializer test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @covers \ArkEcosystem\Crypto\Transactions\Deserializers\Transfer
 */
class TransferTest extends TestCase
{
    /** @test */
    public function it_should_deserialize_the_transaction_signed_with_a_passphrase()
    {
        $fixture = $this->getTransactionFixture('transfer', 'transfer-sign');

        $actual = $this->assertTransaction($fixture);
        $this->assertSame(0, $actual->data['expiration']);
    }

    /** @test */
    public function it_should_deserialize_the_transaction_signed_with_a_second_passphrase()
    {
        $fixture = $this->getTransactionFixture('transfer', 'transfer-secondSign');

        $actual = $this->assertTransaction($fixture);
        $this->assertSame($fixture['data']['secondSignature'], $actual->data['secondSignature']);
    }

    /** @test */
    public function it_should_deserialize_the_transaction_signed_with_a_passphrase_and_vendor_field()
    {
        $fixture = $this->getTransactionFixture('transfer', 'transfer-with-vendor-field-sign');

        $actual = $this->assertTransaction($fixture);
        $this->assertSame($fixture['data']['vendorField'], $actual->data['vendorField']);
    }

    /** @test */
    public function it_should_deserialize_the_transaction_signed_with_a_second_passphrase_and_vendor_field()
    {
        $fixture = $this->getTransactionFixture('transfer', 'transfer-with-vendor-field-secondSign');

        $actual = $this->assertTransaction($fixture);
        $this->assertSame($fixture['data']['vendorField'], $actual->data['vendorField']);
    }

    /** @test */
    public function it_should_deserialize_the_transaction_signed_with_a_passphrase_and_vendor_field_hex()
    {
        //TODO to re-enable ? (fixture)
        $this->markTestIncomplete('This test has not been implemented yet.');
        //$fixture = $this->getTransactionFixture('transfer', 'passphrase-with-vendor-field-hex');
//
        //$actual = $this->assertTransaction($fixture);
        //$this->assertSame(hex2bin($fixture['data']['vendorFieldHex']), $actual->data['vendorField']);
    }

    /** @test */
    public function it_should_deserialize_the_transaction_signed_with_a_second_passphrase_and_vendor_field_hex()
    {
        //TODO to re-enable ? (fixture
        $this->markTestIncomplete('This test has not been implemented yet.');
        //$fixture = $this->getTransactionFixture('transfer', 'second-passphrase-with-vendor-field-hex');
//
        //$actual = $this->assertTransaction($fixture);
        //$this->assertSame(hex2bin($fixture['data']['vendorFieldHex']), $actual->data['vendorField']);
    }

    private function assertTransaction(array $fixture): Transfer
    {
        return $this->assertDeserialized($fixture, [
            'version',
            'network',
            'type',
            'nonce',
            'senderPublicKey',
            'fee',
            'asset',
            'signature',
            'secondSignature',
            'amount',
            'id',
        ]);
    }

    /** @test */
    public function it_should_deserialize_the_transaction_signed_with_a_passphrase_schnorr()
    {
        // This is to test Schnorr signature / verification
        // It doesn't work (doesn't verify, signature does not give the same as fixture...)
        // Haven't found why yet :think:
        $fixture = $this->getTransactionFixture('transfer', 'transfer-sign', true);

        $actual = $this->assertTransaction($fixture);
        $this->assertSame(0, $actual->data['expiration']);
    }
}
