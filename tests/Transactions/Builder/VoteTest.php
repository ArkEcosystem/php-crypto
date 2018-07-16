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

use ArkEcosystem\Crypto\Identity\PublicKey;
use ArkEcosystem\Crypto\Transactions\Builder\Vote;
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
    public function it_should_sign_it_with_a_passphrase()
    {
        $transaction = Vote::new()
            ->votes(['+034151a3ec46b5670a682b0a63394f863587d1bc97483b1b6c70eb58e7f0aed192'])
            ->sign($this->passphrase);

        $this->assertTrue($transaction->verify());
    }

    /** @test */
    public function it_should_sign_it_with_a_second_passphrase()
    {
        $secondPassphrase = 'this is a top secret second passphrase';

        $transaction = Vote::new()
            ->votes(['+034151a3ec46b5670a682b0a63394f863587d1bc97483b1b6c70eb58e7f0aed192'])
            ->sign($this->passphrase)
            ->secondSign($secondPassphrase);

        $this->assertTrue($transaction->verify());
        $this->assertTrue($transaction->secondVerify(PublicKey::fromPassphrase($secondPassphrase)->getHex()));
    }
}