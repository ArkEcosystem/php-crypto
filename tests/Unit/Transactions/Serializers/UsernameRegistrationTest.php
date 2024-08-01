<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Unit\Transactions\Serializers;

use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * @covers \ArkEcosystem\Crypto\Transactions\Types\UsernameRegistration
 */
class UsernameRegistrationTest extends TestCase
{
    /** @test */
    public function it_should_serialize_the_transaction_with_a_passphrase()
    {
        $this->assertSerialized($this->getTransactionFixture('username_registration', 'username-registration-sign'));
    }
}
