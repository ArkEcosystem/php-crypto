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
use ArkEcosystem\Crypto\Transactions\Builder\MultiPaymentBuilder;
use ArkEcosystem\Crypto\Transactions\Serializer;

it('should sign it with a passphrase', function () {
    $transaction = MultiPaymentBuilder::new()
        ->add('AXoXnFi4z1Z6aFvjEYkDVCtBGW2PaRiM25', '100000000')
        ->sign($this->passphrase);

    $this->assertTrue($transaction->verify());
});

it('should sign it with a second passphrase', function () {
    $secondPassphrase = 'this is a top secret second passphrase';

    $transaction = MultiPaymentBuilder::new()
        ->add('AXoXnFi4z1Z6aFvjEYkDVCtBGW2PaRiM25', '100000000')
        ->sign($this->passphrase)
        ->secondSign($secondPassphrase);

    $this->assertTrue($transaction->verify());
    $this->assertTrue($transaction->secondVerify(PublicKey::fromPassphrase($secondPassphrase)->getHex()));
});

it('should match fixture passphrase', function () {
    $fixture = $this->getTransactionFixture('multi_payment', 'multi-payment-sign');
    $builder = MultiPaymentBuilder::new()
        ->add($fixture['data']['asset']['payments'][0]['recipientId'], $fixture['data']['asset']['payments'][0]['amount'])
        ->add($fixture['data']['asset']['payments'][1]['recipientId'], $fixture['data']['asset']['payments'][1]['amount'])
        ->sign($this->passphrase);

    $this->assertTrue($builder->verify());
    $this->assertSame($fixture['serialized'], Serializer::new($builder->transaction)->serialize()->getHex());
    $this->assertSameTransactions($fixture, $builder->transaction->data);
});

it('should match fixture second passphrase', function () {
    $fixture = $this->getTransactionFixture('multi_payment', 'multi-payment-secondSign');
    $builder = MultiPaymentBuilder::new()
        ->add($fixture['data']['asset']['payments'][0]['recipientId'], $fixture['data']['asset']['payments'][0]['amount'])
        ->add($fixture['data']['asset']['payments'][1]['recipientId'], $fixture['data']['asset']['payments'][1]['amount'])
        ->sign($this->passphrase)
        ->secondSign($this->secondPassphrase);

    $this->assertTrue($builder->verify());
    $this->assertSame($fixture['serialized'], Serializer::new($builder->transaction)->serialize()->getHex());
    $this->assertSameTransactions($fixture, $builder->transaction->data);
});

it('should match fixture vendor field passphrase', function () {
    $fixture = $this->getTransactionFixture('multi_payment', 'multi-payment-with-vendor-field-sign');
    unset($fixture['data']['vendorFieldHex']);
    $builder = MultiPaymentBuilder::new()
        ->add($fixture['data']['asset']['payments'][0]['recipientId'], $fixture['data']['asset']['payments'][0]['amount'])
        ->add($fixture['data']['asset']['payments'][1]['recipientId'], $fixture['data']['asset']['payments'][1]['amount'])
        ->vendorField($fixture['data']['vendorField'])
        ->sign($this->passphrase);

    $this->assertTrue($builder->verify());
    $this->assertSame($fixture['serialized'], Serializer::new($builder->transaction)->serialize()->getHex());
    $this->assertSameTransactions($fixture, $builder->transaction->data);
});

it('should match fixture vendor field second passphrase', function () {
    $fixture = $this->getTransactionFixture('multi_payment', 'multi-payment-with-vendor-field-secondSign');
    unset($fixture['data']['vendorFieldHex']);
    $builder = MultiPaymentBuilder::new()
        ->add($fixture['data']['asset']['payments'][0]['recipientId'], $fixture['data']['asset']['payments'][0]['amount'])
        ->add($fixture['data']['asset']['payments'][1]['recipientId'], $fixture['data']['asset']['payments'][1]['amount'])
        ->vendorField($fixture['data']['vendorField'])
        ->sign($this->passphrase)
        ->secondSign($this->secondPassphrase);

    $this->assertTrue($builder->verify());
    $this->assertSame($fixture['serialized'], Serializer::new($builder->transaction)->serialize()->getHex());
    $this->assertSameTransactions($fixture, $builder->transaction->data);
});
