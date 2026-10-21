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

it('should sign it with a passphrase', function () {
    $transaction = HtlcClaimBuilder::new()
        ->htlcClaimAsset(
            'fe1a1b3b117c28078c5d3c42ffb9492234afc01d15b08c047feccf0b6bee0f78',
            'my secret that should be 32bytes'
        )->sign($this->passphrase);

    $this->assertTrue($transaction->verify());
});

it('should sign it with a second passphrase', function () {
    $secondPassphrase = 'this is a top secret second passphrase';

    $transaction = HtlcClaimBuilder::new()
        ->htlcClaimAsset(
            'fe1a1b3b117c28078c5d3c42ffb9492234afc01d15b08c047feccf0b6bee0f78',
            'my secret that should be 32bytes'
        )->sign($this->passphrase)
        ->secondSign($secondPassphrase);

    $this->assertTrue($transaction->verify());
    $this->assertTrue($transaction->secondVerify(PublicKey::fromPassphrase($secondPassphrase)->getHex()));
});

it('should match fixture passphrase', function () {
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
});

it('should match fixture second passphrase', function () {
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
});
