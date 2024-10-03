<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Unit\Transactions\Deserializers;

use ArkEcosystem\Tests\Crypto\TestCase;
use ArkEcosystem\Crypto\Transactions\Types\EvmCall;
use ArkEcosystem\Crypto\Transactions\Types\MultiPayment;

/**
 * @covers \ArkEcosystem\Crypto\Transactions\Types\EvmCall
 */
class EvmCallBuilderTest extends TestCase
{
    /** @test */
    public function it_should_deserialize_the_transaction_signed_with_a_passphrase()
    {
        $fixture = $this->getTransactionFixture('evm_call', 'evm-sign');

        $this->assertTransaction($fixture);
    }

    private function assertTransaction(array $fixture): EvmCall
    {
        $actual = $this->assertDeserialized($fixture, [
            'nonce',
            'fee',
            'gasLimit',
            'contractId',
        ]);

        $this->assertTrue($actual->verify());

        return $actual;
    }
}
