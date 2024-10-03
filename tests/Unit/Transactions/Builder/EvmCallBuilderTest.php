<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Unit\Transactions\Builder;

use ArkEcosystem\Crypto\Transactions\Builder\EvmCallBuilder;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * @covers \ArkEcosystem\Crypto\Transactions\Builder\MultiPaymentBuilder
 */
class EvmCallBuilderTest extends TestCase
{
    /** @test */
    public function it_should_sign_it_with_a_passphrase()
    {
        $fixture = $this->getTransactionFixture('evm_call', 'evm-sign');

        $builder = EvmCallBuilder::new()
          ->withFee($fixture['data']['fee'])
          ->withNonce($fixture['data']['nonce'])
          ->recipient($fixture['data']['contractId'])
          ->payload($fixture['data']['payload'])
          ->gasLimit($fixture['data']['gasLimit'])
          ->sign($this->passphrase);

        $this->assertTrue($builder->verify());
    }
}
