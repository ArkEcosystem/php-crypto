<?php

declare(strict_types=1);



namespace ArkEcosystem\Tests\Crypto\Unit\Transactions\Deserializers;

use ArkEcosystem\Crypto\Transactions\Types\Transfer;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
  * @covers \ArkEcosystem\Crypto\Transactions\Types\Transfer
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
    public function it_should_deserialize_the_transaction_signed_with_a_passphrase_and_vendor_field()
    {
        $fixture = $this->getTransactionFixture('transfer', 'transfer-with-vendor-field-sign');

        $actual = $this->assertTransaction($fixture);
        $this->assertSame($fixture['data']['vendorField'], $actual->data['vendorField']);
    }

    private function assertTransaction(array $fixture): Transfer
    {
        $actual = $this->assertDeserialized($fixture, [
            'version',
            'network',
            'type',
            'typeGroup',
            'nonce',
            'senderPublicKey',
            'fee',
            'asset',
            'signature',
            'amount',
            'id',
        ]);

        $this->assertTrue($actual->verify());

        return $actual;
    }
}
