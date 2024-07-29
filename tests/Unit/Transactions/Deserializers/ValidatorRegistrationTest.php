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

namespace ArkEcosystem\Tests\Crypto\Unit\Transactions\Deserializers;

use ArkEcosystem\Crypto\Transactions\Deserializer;
use ArkEcosystem\Crypto\Transactions\Types\ValidatorRegistration;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * This is the delegate registration deserializer test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @covers \ArkEcosystem\Crypto\Transactions\Types\ValidatorRegistration
 */
class ValidatorRegistrationTest extends TestCase
{
    /** @test */
    public function it_should_deserialize_the_transaction_signed_with_a_passphrase()
    {
        $fixture = $this->getTransactionFixture('validator_registration', 'validator-registration-sign');

        $this->assertTransaction($fixture);
    }

    private function assertTransaction(array $fixture): ValidatorRegistration
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