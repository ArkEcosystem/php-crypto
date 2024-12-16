<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Unit\Transactions\Builder;

use ArkEcosystem\Crypto\Exceptions\InvalidUsernameException;
use ArkEcosystem\Crypto\Transactions\Builder\UsernameRegistrationBuilder;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * @covers \ArkEcosystem\Crypto\Transactions\Builder\UsernameRegistrationBuilder
 */
class UsernameRegistrationBuilderTest extends TestCase
{
    /** @test */
    public function it_should_sign_it_with_a_passphrase()
    {
        $fixture = $this->getTransactionFixture('evm_call', 'username-registration');

        $builder = UsernameRegistrationBuilder::new()
          ->gasPrice($fixture['data']['gasPrice'])
          ->nonce($fixture['data']['nonce'])
          ->network($fixture['data']['network'])
          ->gasLimit($fixture['data']['gasLimit'])
          ->recipientAddress($fixture['data']['recipientAddress'])
          ->username('php')
          ->sign($this->passphrase);

        $this->assertSame($fixture['serialized'], $builder->transaction->serialize()->getHex());

        $this->assertSame($fixture['data']['id'], $builder->transaction->data['id']);

        $this->assertTrue($builder->verify());
    }

    /** @test */
    public function it_should_throw_exception_for_invalid_username()
    {
        $this->expectException(InvalidUsernameException::class);
          $this->expectExceptionMessage('Username must be between 1 and 20 characters long');

        $fixture = $this->getTransactionFixture('evm_call', 'username-registration');

        $builder = UsernameRegistrationBuilder::new()
          ->gasPrice($fixture['data']['gasPrice'])
          ->nonce($fixture['data']['nonce'])
          ->network($fixture['data']['network'])
          ->gasLimit($fixture['data']['gasLimit'])
          ->recipientAddress($fixture['data']['recipientAddress'])
          ->username('this_is_a_very_long_username_that_is_invalid')
          ->sign($this->passphrase);
    }
}
