<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Unit\Transactions\Builder;

use ArkEcosystem\Crypto\Transactions\Builder\ValidatorRegistrationBuilder;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * @covers \ArkEcosystem\Crypto\Transactions\Builder\ValidatorRegistrationBuilder
 */
class ValidatorRegistrationBuilderTest extends TestCase
{
    /** @test */
    public function it_should_sign_it_with_a_passphrase()
    {
        $fixture = $this->getTransactionFixture('evm_call', 'validator-registration');

        $builder = ValidatorRegistrationBuilder::new()
            ->gasPrice($fixture['data']['gasPrice'])
            ->nonce($fixture['data']['nonce'])
            ->network($fixture['data']['network'])
            ->gasLimit($fixture['data']['gasLimit'])
            ->validatorPublicKey('954f46d6097a1d314e900e66e11e0dad0a57cd03e04ec99f0dedd1c765dcb11e6d7fa02e22cf40f9ee23d9cc1c0624bd')
            ->sign($this->passphrase);

        $this->assertSame($fixture['data']['gasPrice'], $builder->transaction->data['gasPrice']);
        $this->assertSame($fixture['data']['nonce'], $builder->transaction->data['nonce']);
        $this->assertSame($fixture['data']['network'], $builder->transaction->data['network']);
        $this->assertSame($fixture['data']['gasLimit'], $builder->transaction->data['gasLimit']);
        $this->assertSame($fixture['data']['v'], $builder->transaction->data['v']);
        $this->assertSame($fixture['data']['r'], $builder->transaction->data['r']);
        $this->assertSame($fixture['data']['s'], $builder->transaction->data['s']);

        $this->assertSame($fixture['serialized'], $builder->transaction->serialize()->getHex());

        $this->assertSame($fixture['data']['id'], $builder->transaction->data['id']);

        $this->assertTrue($builder->verify());
    }
}
