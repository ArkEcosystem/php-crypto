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
            ->add('0xb693449AdDa7EFc015D87944EAE8b7C37EB1690A', '100000000')
            ->sign($this->passphrase);

        $this->assertTrue($transaction->verify());
    }

    /** @test */
    public function it_should_multi_sign()
    {
        $fixture = $this->getTransactionFixture('multi_payment', 'multi-payment-multi-sign');

        $builder = MultiPaymentBuilder::new()
            ->withNonce($fixture['data']['nonce'])
            ->withNetwork($fixture['data']['network'])
            ->add($fixture['data']['asset']['payments'][0]['recipientId'], $fixture['data']['asset']['payments'][0]['amount'])
            ->add($fixture['data']['asset']['payments'][1]['recipientId'], $fixture['data']['asset']['payments'][1]['amount']);

        foreach ($this->passphrases as $index => $passphrase) {
            $builder->multiSign($passphrase, $index);
        }

        $builder->sign($this->passphrase);

        $this->assertTrue($builder->verify());

        $this->assertSameSerializationMultisignature($fixture['serialized'], Serializer::new($builder->transaction)->serialize()->getHex(), 3);

        $this->assertSameTransactions($fixture, $builder->transaction->data);
    }

    /** @test */
    public function it_should_match_fixture_passphrase()
    {
        $fixture = $this->getTransactionFixture('multi_payment', 'multi-payment-sign');
        $builder = MultiPaymentBuilder::new()
            ->withNonce($fixture['data']['nonce'])
            ->withNetwork($fixture['data']['network'])
            ->add($fixture['data']['asset']['payments'][0]['recipientId'], $fixture['data']['asset']['payments'][0]['amount'])
            ->add($fixture['data']['asset']['payments'][1]['recipientId'], $fixture['data']['asset']['payments'][1]['amount'])
            ->sign($this->passphrase);

        $this->assertTrue($builder->verify());
        $this->assertSameSerialization($fixture['serialized'], Serializer::new($builder->transaction)->serialize()->getHex());
        $this->assertSameTransactions($fixture, $builder->transaction->data);
    }

    /** @test */
    public function it_should_match_fixture_vendor_field_passphrase()
    {
        $fixture = $this->getTransactionFixture('multi_payment', 'multi-payment-with-vendor-field-sign');
        unset($fixture['data']['vendorFieldHex']);
        $builder = MultiPaymentBuilder::new()
            ->withNonce($fixture['data']['nonce'])
            ->withNetwork($fixture['data']['network'])
            ->add($fixture['data']['asset']['payments'][0]['recipientId'], $fixture['data']['asset']['payments'][0]['amount'])
            ->add($fixture['data']['asset']['payments'][1]['recipientId'], $fixture['data']['asset']['payments'][1]['amount'])
            ->vendorField($fixture['data']['vendorField'])
            ->sign($this->passphrase);

        $this->assertTrue($builder->verify());
        $this->assertSameSerialization($fixture['serialized'], Serializer::new($builder->transaction)->serialize()->getHex());
        $this->assertSameTransactions($fixture, $builder->transaction->data);
    }
}
