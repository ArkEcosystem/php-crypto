<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Unit\Transactions\Serializers;

use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * @covers \ArkEcosystem\Crypto\Transactions\Types\Transfer
 */
class TransferTest extends TestCase
{
    /** @test */
    public function it_should_serialize_the_transaction_with_a_passphrase()
    {
        $this->assertSerialized($this->getTransactionFixture('transfer', 'transfer-sign'));
    }

    /** @test */
    public function it_should_serialize_the_transaction_with_a_passphrase_and_vendor_field()
    {
        $this->assertSerialized($this->getTransactionFixture('transfer', 'transfer-with-vendor-field-sign'));
    }
}
