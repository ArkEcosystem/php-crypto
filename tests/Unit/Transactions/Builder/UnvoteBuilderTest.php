<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Unit\Transactions\Builder;

use ArkEcosystem\Crypto\Transactions\Builder\UnvoteBuilder;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * @covers \ArkEcosystem\Crypto\Transactions\Builder\UnvoteBuilder
 */
class UnvoteBuilderTest extends TestCase
{
    /** @test */
    public function it_should_sign_it_with_a_passphrase()
    {
        $fixture = $this->getTransactionFixture('evm_call', 'unvote');

        $builder = UnvoteBuilder::new()
          ->gasPrice($fixture['data']['gasPrice'])
          ->nonce($fixture['data']['nonce'])
          ->network($fixture['data']['network'])
          ->gasLimit($fixture['data']['gasLimit'])
          ->recipientAddress($fixture['data']['recipientAddress'])
          ->sign($this->passphrase);

        $this->assertSame($fixture['serialized'], $builder->transaction->serialize()->getHex());

        $this->assertSame($fixture['data']['id'], $builder->transaction->data['id']);

        $this->assertTrue($builder->verify());
    }
}
