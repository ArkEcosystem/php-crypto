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

use ArkEcosystem\Crypto\Builder\Vote;
use ArkEcosystem\Crypto\Utils\Crypto;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * This is the vote builder test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @coversNothing
 */
class VoteTest extends TestCase
{
    /** @test */
    public function it_should_create_a_valid_transaction()
    {
        $transaction = Vote::new()
            ->votes(['+034151a3ec46b5670a682b0a63394f863587d1bc97483b1b6c70eb58e7f0aed192'])
            ->sign('This is a top secret passphrase');

        $this->assertTrue($transaction->verify());
    }
}
