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
use ArkEcosystem\Crypto\Transactions\Builder\HtlcLockBuilder;
use ArkEcosystem\Crypto\Transactions\Serializer;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * This is the delegate registration builder test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @covers \ArkEcosystem\Crypto\Transactions\Builder\HtlcLockBuilder
 */
class HtlcLockTest extends TestCase
{
    /** @test */
    public function it_should_sign_it_with_a_passphrase()
    {
        $transaction = HtlcLockBuilder::new()
            ->htlcLockAsset(
                '0f128d401958b1b30ad0d10406f47f9489321017b4614e6cb993fc63913c5454',
                1,
                1
            )
            ->recipient('ANBkoGqWeTSiaEVgVzSKZd3jS7UWzv9PSo')
            ->sign($this->passphrase);

        $this->assertTrue($transaction->verify());
    }

    /** @test */
    public function it_should_sign_it_with_a_second_passphrase()
    {
        $secondPassphrase = 'this is a top secret second passphrase';

        $transaction = HtlcLockBuilder::new()
            ->htlcLockAsset(
                '0f128d401958b1b30ad0d10406f47f9489321017b4614e6cb993fc63913c5454',
                1,
                1
            )
            ->recipient('ANBkoGqWeTSiaEVgVzSKZd3jS7UWzv9PSo')
            ->sign($this->passphrase)
            ->secondSign($secondPassphrase);

        $this->assertTrue($transaction->verify());
        $this->assertTrue($transaction->secondVerify(PublicKey::fromPassphrase($secondPassphrase)->getHex()));
    }

    /** @test */
    public function it_should_match_fixture_passphrase()
    {
        $fixture = $this->getTransactionFixture('htlc_lock', 'htlc-lock-sign');
        $builder = HtlcLockBuilder::new()
            ->recipient($fixture['data']['recipientId'])
            ->amount($fixture['data']['amount'])
            ->htlcLockAsset(
                $fixture['data']['asset']['lock']['secretHash'],
                $fixture['data']['asset']['lock']['expiration']['type'],
                $fixture['data']['asset']['lock']['expiration']['value']
                )
            ->sign($this->passphrase);

        $this->assertTrue($builder->verify());
        $this->assertSame($fixture['serialized'], Serializer::new($builder->transaction)->serialize()->getHex());
        $this->assertSameTransactions($fixture, $builder->transaction->data);
    }

    /** @test */
    public function it_should_match_fixture_second_passphrase()
    {
        $fixture = $this->getTransactionFixture('htlc_lock', 'htlc-lock-secondSign');
        $builder = HtlcLockBuilder::new()
            ->recipient($fixture['data']['recipientId'])
            ->amount($fixture['data']['amount'])
            ->htlcLockAsset(
                $fixture['data']['asset']['lock']['secretHash'],
                $fixture['data']['asset']['lock']['expiration']['type'],
                $fixture['data']['asset']['lock']['expiration']['value']
                )
            ->sign($this->passphrase)
            ->secondSign($this->secondPassphrase);

        $this->assertTrue($builder->verify());
        $this->assertSame($fixture['serialized'], Serializer::new($builder->transaction)->serialize()->getHex());
        $this->assertSameTransactions($fixture, $builder->transaction->data);
    }

    /** @test */
    public function it_should_match_fixture_vendor_field_passphrase()
    {
        $fixture = $this->getTransactionFixture('htlc_lock', 'htlc-lock-with-vendor-field-sign');
        unset($fixture['data']['vendorFieldHex']);
        $builder = HtlcLockBuilder::new()
            ->recipient($fixture['data']['recipientId'])
            ->amount($fixture['data']['amount'])
            ->htlcLockAsset(
                $fixture['data']['asset']['lock']['secretHash'],
                $fixture['data']['asset']['lock']['expiration']['type'],
                $fixture['data']['asset']['lock']['expiration']['value']
                )
            ->vendorField($fixture['data']['vendorField'])
            ->sign($this->passphrase);

        $this->assertTrue($builder->verify());
        $this->assertSame($fixture['serialized'], Serializer::new($builder->transaction)->serialize()->getHex());
        $this->assertSameTransactions($fixture, $builder->transaction->data);
    }

    /** @test */
    public function it_should_match_fixture_vendor_field_second_passphrase()
    {
        $fixture = $this->getTransactionFixture('htlc_lock', 'htlc-lock-with-vendor-field-secondSign');
        unset($fixture['data']['vendorFieldHex']);
        $builder = HtlcLockBuilder::new()
            ->recipient($fixture['data']['recipientId'])
            ->amount($fixture['data']['amount'])
            ->htlcLockAsset(
                $fixture['data']['asset']['lock']['secretHash'],
                $fixture['data']['asset']['lock']['expiration']['type'],
                $fixture['data']['asset']['lock']['expiration']['value']
                )
            ->vendorField($fixture['data']['vendorField'])
            ->sign($this->passphrase)
            ->secondSign($this->secondPassphrase);

        $this->assertTrue($builder->verify());
        $this->assertSame($fixture['serialized'], Serializer::new($builder->transaction)->serialize()->getHex());
        $this->assertSameTransactions($fixture, $builder->transaction->data);
    }
}
