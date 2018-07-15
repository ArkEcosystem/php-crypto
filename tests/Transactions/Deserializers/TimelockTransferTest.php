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
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * This is the timelock transfer deserializer test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @coversNothing
 */
class TimelockTransferTest extends TestCase
{
    /** @test */
    public function it_should_deserialize_the_transaction_signed_with_a_passphrase()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');

        $transaction = $this->getTransactionFixture('timelock_transfer', 'passphrase');

        $this->assertTransaction($transaction);
    }
}
