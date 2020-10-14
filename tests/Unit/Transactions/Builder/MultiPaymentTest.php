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
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * This is the multi payment builder test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @covers \ArkEcosystem\Crypto\Transactions\Builder\MultiPaymentBuilder
 */
class MultiPaymentTest extends TestCase
{
    /** @test */
    public function it_should_sign_it_with_a_passphrase()
    {
        $transaction = MultiPaymentBuilder::new()
            ->add('AXoXnFi4z1Z6aFvjEYkDVCtBGW2PaRiM25', '100000000')
            ->sign($this->passphrase);

        $this->assertTrue($transaction->verify());
    }

    /** @test */
    public function it_should_sign_it_with_a_second_passphrase()
    {
        $secondPassphrase = 'this is a top secret second passphrase';

        $transaction = MultiPaymentBuilder::new()
            ->add('AXoXnFi4z1Z6aFvjEYkDVCtBGW2PaRiM25', '100000000')
            ->sign($this->passphrase)
            ->secondSign($secondPassphrase);

        $this->assertTrue($transaction->verify());
        $this->assertTrue($transaction->secondVerify(PublicKey::fromPassphrase($secondPassphrase)->getHex()));
    }

    /** @test */
    public function it_should_match_fixture_passphrase()
    {
        $fixture = $this->getTransactionFixture('multi_payment', 'multi-payment-sign');
        $builder = MultiPaymentBuilder::new()
            ->add($fixture['data']['asset']['payments'][0]['recipientId'], $fixture['data']['asset']['payments'][0]['amount'])
            ->add($fixture['data']['asset']['payments'][1]['recipientId'], $fixture['data']['asset']['payments'][1]['amount'])
            ->sign($this->passphrase);

        $this->assertTrue($builder->verify());
        $this->assertSame($fixture['serialized'], Serializer::new($builder->transaction)->serialize()->getHex());
        $this->assertSameTransactions($fixture, $builder->transaction->data);
    }

    /** @test */
    public function it_should_match_fixture_second_passphrase()
    {
        $fixture = $this->getTransactionFixture('multi_payment', 'multi-payment-secondSign');
        $builder = MultiPaymentBuilder::new()
            ->add($fixture['data']['asset']['payments'][0]['recipientId'], $fixture['data']['asset']['payments'][0]['amount'])
            ->add($fixture['data']['asset']['payments'][1]['recipientId'], $fixture['data']['asset']['payments'][1]['amount'])
            ->sign($this->passphrase)
            ->secondSign($this->secondPassphrase);

        $this->assertTrue($builder->verify());
        $this->assertSame($fixture['serialized'], Serializer::new($builder->transaction)->serialize()->getHex());
        $this->assertSameTransactions($fixture, $builder->transaction->data);
    }

    /** @test */
    public function it_should_match_fixture_vendor_field_passphrase()
    {
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
    }

    /** @test */
    public function it_should_match_fixture_vendor_field_second_passphrase()
    {
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
    }
}
