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

use ArkEcosystem\Crypto\Transactions\Types\ValidatorResignation;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
  * @covers \ArkEcosystem\Crypto\Transactions\Types\ValidatorResignation
  */
class ValidatorResignationTest extends TestCase
{
    /** @test */
    public function it_should_deserialize_the_transaction_signed_with_a_passphrase()
    {
        $transaction = $this->getTransactionFixture('validator_resignation', 'validator-resignation-sign');

        $this->assertTransaction($transaction);
    }

    private function assertTransaction(array $fixture): ValidatorResignation
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
