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

use ArkEcosystem\Crypto\Transactions\Builder\TransferBuilder;
use ArkEcosystem\Crypto\Transactions\Serializer;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * This is the transfer builder test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @covers \ArkEcosystem\Crypto\Transactions\Builder\TransferBuilder
 */
class TransferTest extends TestCase
{
    /** @test */
    public function it_should_sign_it_with_a_passphrase()
    {
        $transaction = TransferBuilder::new()
            ->recipient('0xb693449AdDa7EFc015D87944EAE8b7C37EB1690A')
            ->amount('133380000000')
            ->vendorField('This is a transaction from PHP')
            ->sign($this->passphrase);

        $this->assertTrue($transaction->verify());
    }

    /** @test */
    public function it_should_sign_with_multisignature()
    {
        $fixture = $this->getTransactionFixture('transfer', 'transfer-multi-sign');

        $builder = TransferBuilder::new()
            ->recipient($fixture['data']['recipientId'])
            ->amount($fixture['data']['amount'])
            ->withFee($fixture['data']['fee'])
            ->withNonce($fixture['data']['nonce'])
            ->withNetwork($fixture['data']['network']);
            
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
        $fixture = $this->getTransactionFixture('transfer', 'transfer-sign');

        $builder = TransferBuilder::new()
            ->recipient($fixture['data']['recipientId'])
            ->amount($fixture['data']['amount'])
            ->withFee($fixture['data']['fee'])
            ->withNonce($fixture['data']['nonce'])
            ->withNetwork($fixture['data']['network'])
            ->sign($this->passphrase);

        $this->assertTrue($builder->verify());

        $this->assertSameSerialization($fixture['serialized'], Serializer::new($builder->transaction)->serialize()->getHex());

        $this->assertSameTransactions($fixture, $builder->transaction->data);
    }

    /** @test */
    public function it_should_match_fixture_vendor_field_passphrase()
    {
        $fixture = $this->getTransactionFixture('transfer', 'transfer-with-vendor-field-sign');
        unset($fixture['data']['vendorFieldHex']);
        $builder = TransferBuilder::new()
            ->recipient($fixture['data']['recipientId'])
            ->amount($fixture['data']['amount'])
            ->withFee($fixture['data']['fee'])
            ->withNonce($fixture['data']['nonce'])
            ->withNetwork($fixture['data']['network'])
            ->vendorField($fixture['data']['vendorField'])

            ->sign($this->passphrase);

        $this->assertTrue($builder->verify());
        $this->assertSameSerialization($fixture['serialized'], Serializer::new($builder->transaction)->serialize()->getHex());
        $this->assertSameTransactions($fixture, $builder->transaction->data);
    }
}
