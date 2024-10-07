<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Unit\Transactions\Builder;

use ArkEcosystem\Tests\Crypto\TestCase;
use ArkEcosystem\Crypto\Identities\PublicKey;
use ArkEcosystem\Crypto\Transactions\Builder\EvmCallBuilder;
use ArkEcosystem\Crypto\Transactions\Builder\MultiPaymentBuilder;

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
          ->withNetwork($fixture['data']['network'])
          ->payload($fixture['data']['asset']['evmCall']['payload'])      
          ->withGasLimit($fixture['data']['asset']['evmCall']['gasLimit'])
          ->sign($this->passphrase);
          
        $this->assertTrue($builder->verify());
    }
}
