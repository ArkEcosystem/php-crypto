<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Unit\Transactions\Builder;

use ArkEcosystem\Crypto\Transactions\Builder\MultipaymentBuilder;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * @covers \ArkEcosystem\Crypto\Transactions\Builder\MultipaymentBuilder
 */
class MultipaymentBuilderTest extends TestCase
{
    // /** @test */
    // public function it_should_sign_it_with_a_passphrase()
    // {
    //     $fixture = $this->getTransactionFixture('evm_call', 'username-registration');

    //     $builder = UsernameRegistrationBuilder::new()
    //       ->gasPrice($fixture['data']['gasPrice'])
    //       ->nonce($fixture['data']['nonce'])
    //       ->network($fixture['data']['network'])
    //       ->gasLimit($fixture['data']['gasLimit'])
    //       ->username('php')
    //       ->sign($this->passphrase);

    //     $this->assertSame($fixture['serialized'], $builder->transaction->serialize()->getHex());

    //     $this->assertSame($fixture['data']['id'], $builder->transaction->data['id']);

    //     $this->assertTrue($builder->verify());
    // }

    /** @test */
    public function it_should_handle_single_recipient()
    {
        $fixture = $this->getTransactionFixture('evm_call', 'multipayment-1');

        $builder = MultipaymentBuilder::new()
          ->gasPrice($fixture['data']['gasPrice'])
          ->nonce($fixture['data']['nonce'])
          ->network($fixture['data']['network'])
          ->gasLimit($fixture['data']['gasLimit'])
          ->pay('0x8233F6Df6449D7655f4643D2E752DC8D2283fAd5', '1000000000000000000')
          ->sign($this->passphrase);

        $this->assertSame($fixture['serialized'], $builder->transaction->serialize()->getHex());

        $this->assertSame($fixture['data']['id'], $builder->transaction->data['id']);

        $this->assertTrue($builder->verify());
    }

    // /** @test */
    // public function it_should_handle_sending_to_self()
    // {

    // }

    /* @test */
    // public function it_should_handle_empty_payment()
    // {
    //     $fixture = $this->getTransactionFixture('evm_call', 'multipayment-0');

    //     $builder = MultipaymentBuilder::new()
    //       ->gasPrice($fixture['data']['gasPrice'])
    //       ->nonce($fixture['data']['nonce'])
    //       ->network($fixture['data']['network'])
    //       ->gasLimit($fixture['data']['gasLimit'])
    //       ->sign($this->passphrase);

    //     $this->assertSame($fixture['serialized'], $builder->transaction->serialize()->getHex());

    //     $this->assertSame($fixture['data']['id'], $builder->transaction->data['id']);

    //     $this->assertTrue($builder->verify());
    // }
}
