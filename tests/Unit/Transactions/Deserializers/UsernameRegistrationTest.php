<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Unit\Transactions\Deserializers;

use ArkEcosystem\Crypto\Transactions\Types\UsernameRegistration;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * @covers \ArkEcosystem\Crypto\Transactions\Types\UsernameRegistration
 */
class UsernameRegistrationTest extends TestCase
{
    /** @test */
    public function it_should_deserialize_the_transaction_signed_with_a_passphrase()
    {
        $fixture = $this->getTransactionFixture('username_registration', 'username-registration-sign');

        $this->assertTransaction($fixture);
    }

    private function assertTransaction(array $fixture): UsernameRegistration
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
            'secondSignature',
            'amount',
            'id',
        ]);

        $this->assertTrue($actual->verify());

        return $actual;
    }
}
