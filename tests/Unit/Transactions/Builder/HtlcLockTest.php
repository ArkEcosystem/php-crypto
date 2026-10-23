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

it('should sign it with a passphrase', function () {
    $transaction = HtlcLockBuilder::new()
        ->htlcLockAsset(
            '0f128d401958b1b30ad0d10406f47f9489321017b4614e6cb993fc63913c5454',
            1,
            1
        )
        ->recipient('ANBkoGqWeTSiaEVgVzSKZd3jS7UWzv9PSo')
        ->sign($this->passphrase);

    $this->assertTrue($transaction->verify());
});

it('should sign it with a second passphrase', function () {
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
});

it('should match fixture passphrase', function () {
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
});

it('should match fixture second passphrase', function () {
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
});

it('should match fixture vendor field passphrase', function () {
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
});

it('should match fixture vendor field second passphrase', function () {
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
});
