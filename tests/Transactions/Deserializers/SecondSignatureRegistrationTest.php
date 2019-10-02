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

use ArkEcosystem\Tests\Crypto\TestCase;
use ArkEcosystem\Crypto\Identities\Address;
use ArkEcosystem\Crypto\Transactions\Transaction;
use ArkEcosystem\Crypto\Transactions\Deserializer;
use ArkEcosystem\Crypto\Transactions\Types\SecondSignatureRegistration;

/**
 * This is the second signature registration deserializer test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @covers \ArkEcosystem\Crypto\Transactions\Types\SecondSignatureRegistration
 */
class SecondSignatureRegistrationTest extends TestCase
{
    /** @test */
    public function it_should_deserialize_the_transaction_signed_with_a_second_passphrase()
    {
        $transaction = $this->getTransactionFixture('second_signature_registration', 'passphrase');

        $actual = $this->assertTransaction($transaction);
    }

    private function assertTransaction(array $fixture): SecondSignatureRegistration
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
}
