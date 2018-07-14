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

namespace ArkEcosystem\Tests\Crypto\Builder;

use ArkEcosystem\Crypto\Builder\TimelockTransfer;
use ArkEcosystem\Crypto\Utils\Crypto;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * This is the timelock transfer builder test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @coversNothing
 */
class TimelockTransferTest extends TestCase
{
    /** @test */
    public function it_should_create_a_valid_transaction()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');

        $transaction = TimelockTransfer::new()
            ->timestamp()
            ->timelock(time())
            ->recipient('AXoXnFi4z1Z6aFvjEYkDVCtBGW2PaRiM25')
            ->amount(133380000000)
            ->vendorField('This is a transaction from PHP')
            ->sign('This is a top secret passphrase');

        $this->assertInternalType('object', $transaction);
        $this->assertTrue($transaction->verify());
    }
}
