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

namespace ArkEcosystem\Tests\Crypto\Transactions\Builder;

use ArkEcosystem\Crypto\Transactions\Builder\Transfer;
use ArkEcosystem\Crypto\Utils\Crypto;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * This is the transfer builder test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @coversNothing
 */
class TransferTest extends TestCase
{
    /** @test */
    public function it_should_create_a_valid_transaction()
    {
        $transaction = Transfer::new()
            ->recipient('AXoXnFi4z1Z6aFvjEYkDVCtBGW2PaRiM25')
            ->amount(133380000000)
            ->vendorField('This is a transaction from PHP')
            ->sign('This is a top secret passphrase');

        $this->assertTrue($transaction->verify());
    }

    /** @test */
    public function it_should_create_a_valid_transaction_using_a_second_secret()
    {
        $secondSecret = 'this is a top secret second passphrase';

        $transaction = Transfer::new()
            ->recipient('AXoXnFi4z1Z6aFvjEYkDVCtBGW2PaRiM25')
            ->amount(133380000000)
            ->vendorField('This is a transaction from PHP')
            ->sign('This is a top secret passphrase')
            ->secondSign($secondSecret);

        $this->assertTrue($transaction->verify());
        $this->assertTrue($transaction->secondVerify($secondSecret));
    }
}
