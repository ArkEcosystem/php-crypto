<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Unit\Transactions\Builder;

use ArkEcosystem\Crypto\Transactions\Builder\EvmCallBuilder;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * @covers \ArkEcosystem\Crypto\Transactions\Builder\EvmCallBuilder
 */
class EvmCallBuilderTest extends TestCase
{
    /** @test */
    public function it_should_sign_it_with_a_passphrase()
    {
        $fixture = $this->getTransactionFixture('evm_call', 'evm-sign');

        $builder = EvmCallBuilder::new()
          ->gasPrice($fixture['data']['gasPrice'])
          ->nonce($fixture['data']['nonce'])
          ->network($fixture['data']['network'])
          ->payload($fixture['data']['data'])
          ->gasLimit($fixture['data']['gasLimit'])
          ->recipientAddress('0xE536720791A7DaDBeBdBCD8c8546fb0791a11901')
          ->sign($this->passphrase);

        $this->assertTrue($builder->verify());
    }
}
