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
use ArkEcosystem\Crypto\Transactions\Builder\IPFSBuilder;
use ArkEcosystem\Crypto\Transactions\Serializer;

it('should sign it with a passphrase', function () {
    $transaction = IPFSBuilder::new()
        ->ipfsAsset('QmR45FmbVVrixReBwJkhEKde2qwHYaQzGxu4ZoDeswuF9w')
        ->sign($this->passphrase);

    $this->assertTrue($transaction->verify());
});

it('should sign it with a second passphrase', function () {
    $secondPassphrase = 'this is a top secret second passphrase';

    $transaction = IPFSBuilder::new()
        ->ipfsAsset('QmR45FmbVVrixReBwJkhEKde2qwHYaQzGxu4ZoDeswuF9w')
        ->sign($this->passphrase)
        ->secondSign($secondPassphrase);

    $this->assertTrue($transaction->verify());
    $this->assertTrue($transaction->secondVerify(PublicKey::fromPassphrase($secondPassphrase)->getHex()));
});

it('should match fixture passphrase', function () {
    $fixture = $this->getTransactionFixture('ipfs', 'ipfs-sign');
    $builder = IPFSBuilder::new()
        ->ipfsAsset($fixture['data']['asset']['ipfs'])
        ->sign($this->passphrase);

    $this->assertTrue($builder->verify());
    $this->assertSame($fixture['serialized'], Serializer::new($builder->transaction)->serialize()->getHex());
    $this->assertSameTransactions($fixture, $builder->transaction->data);
});

it('should match fixture second passphrase', function () {
    $fixture = $this->getTransactionFixture('ipfs', 'ipfs-secondSign');
    $builder = IPFSBuilder::new()
        ->ipfsAsset($fixture['data']['asset']['ipfs'])
        ->sign($this->passphrase)
        ->secondSign($this->secondPassphrase);

    $this->assertTrue($builder->verify());
    $this->assertSame($fixture['serialized'], Serializer::new($builder->transaction)->serialize()->getHex());
    $this->assertSameTransactions($fixture, $builder->transaction->data);
});
