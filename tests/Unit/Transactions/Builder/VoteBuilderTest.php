<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Unit\Transactions\Builder;

use ArkEcosystem\Crypto\Transactions\Builder\VoteBuilder;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * @covers \ArkEcosystem\Crypto\Transactions\Builder\VoteBuilder
 */
class VoteBuilderTest extends TestCase
{
    /** @test */
    public function it_should_sign_it_with_a_passphrase()
    {
        $fixture = $this->getTransactionFixture('evm_call', 'vote');

        $builder = VoteBuilder::new()
          ->gasPrice($fixture['data']['gasPrice'])
          ->nonce($fixture['data']['nonce'])
          ->network($fixture['data']['network'])
          ->vote('0xC3bBE9B1CeE1ff85Ad72b87414B0E9B7F2366763')
          ->gasLimit($fixture['data']['gasLimit'])
          ->sign($this->passphrase);

        $this->assertSame($fixture['serialized'], $builder->transaction->serialize()->getHex());

        $this->assertSame($fixture['data']['id'], $builder->transaction->data['id']);

        $this->assertTrue($builder->verify());
    }
}
