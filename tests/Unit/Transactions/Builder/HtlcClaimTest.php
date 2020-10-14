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

namespace ArkEcosystem\Tests\Crypto\Unit\Transactions\Builder;

use ArkEcosystem\Crypto\Identities\PublicKey;
use ArkEcosystem\Crypto\Transactions\Builder\HtlcClaimBuilder;
use ArkEcosystem\Crypto\Transactions\Serializer;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * This is the delegate registration builder test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @covers \ArkEcosystem\Crypto\Transactions\Builder\HtlcClaimBuilder
 */
class HtlcClaimTest extends TestCase
{
    /** @test */
    public function it_should_sign_it_with_a_passphrase()
    {
        $transaction = HtlcClaimBuilder::new()
            ->htlcClaimAsset(
                'fe1a1b3b117c28078c5d3c42ffb9492234afc01d15b08c047feccf0b6bee0f78',
                'my secret that should be 32bytes'
            )->sign($this->passphrase);

        $this->assertTrue($transaction->verify());
    }

    /** @test */
    public function it_should_sign_it_with_a_second_passphrase()
    {
        $secondPassphrase = 'this is a top secret second passphrase';

        $transaction = HtlcClaimBuilder::new()
            ->htlcClaimAsset(
                'fe1a1b3b117c28078c5d3c42ffb9492234afc01d15b08c047feccf0b6bee0f78',
                'my secret that should be 32bytes'
            )->sign($this->passphrase)
            ->secondSign($secondPassphrase);

        $this->assertTrue($transaction->verify());
        $this->assertTrue($transaction->secondVerify(PublicKey::fromPassphrase($secondPassphrase)->getHex()));
    }

    /** @test */
    public function it_should_match_fixture_passphrase()
    {
        $fixture = $this->getTransactionFixture('htlc_claim', 'htlc-claim-sign');
        $builder = HtlcClaimBuilder::new()
            ->htlcClaimAsset(
                $fixture['data']['asset']['claim']['lockTransactionId'],
                $fixture['data']['asset']['claim']['unlockSecret']
                )
            ->sign($this->passphrase);

        $this->assertTrue($builder->verify());
        $this->assertSame($fixture['serialized'], Serializer::new($builder->transaction)->serialize()->getHex());
        $this->assertSameTransactions($fixture, $builder->transaction->data);
    }

    /** @test */
    public function it_should_match_fixture_second_passphrase()
    {
        $fixture = $this->getTransactionFixture('htlc_claim', 'htlc-claim-secondSign');
        $builder = HtlcClaimBuilder::new()
            ->htlcClaimAsset(
                $fixture['data']['asset']['claim']['lockTransactionId'],
                $fixture['data']['asset']['claim']['unlockSecret']
                )
            ->sign($this->passphrase)
            ->secondSign($this->secondPassphrase);

        $this->assertTrue($builder->verify());
        $this->assertSame($fixture['serialized'], Serializer::new($builder->transaction)->serialize()->getHex());
        $this->assertSameTransactions($fixture, $builder->transaction->data);
    }
}
