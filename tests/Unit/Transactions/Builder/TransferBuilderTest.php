<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Unit\Transactions\Builder;

use ArkEcosystem\Crypto\Transactions\Builder\TransferBuilder;
use ArkEcosystem\Crypto\Utils\UnitConverter;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * @covers \ArkEcosystem\Crypto\Transactions\Builder\TransferBuilder
 */
class TransferBuilderTest extends TestCase
{
    /** @test */
    public function it_should_sign_it_with_a_passphrase()
    {
        $fixture = $this->getTransactionFixture('evm_call', 'transfer');

        $builder = TransferBuilder::new()
            ->gasPrice(UnitConverter::parseUnits($fixture['data']['gasPrice'], 'wei'))
            ->nonce($fixture['data']['nonce'])
            ->network($fixture['data']['network'])
            ->gasLimit(UnitConverter::parseUnits($fixture['data']['gasLimit'], 'wei'))
            ->recipientAddress($fixture['data']['recipientAddress'])
            ->value(UnitConverter::parseUnits($fixture['data']['value'], 'wei'))
            ->sign($this->passphrase);

        $this->assertSame((string) $fixture['data']['gasPrice'], (string) $builder->transaction->data['gasPrice']);
        $this->assertSame($fixture['data']['nonce'], $builder->transaction->data['nonce']);
        $this->assertSame($fixture['data']['network'], $builder->transaction->data['network']);
        $this->assertSame((string) $fixture['data']['gasLimit'], (string) $builder->transaction->data['gasLimit']);
        $this->assertSame($fixture['data']['recipientAddress'], $builder->transaction->data['recipientAddress']);
        $this->assertSame((string) $fixture['data']['value'], (string) $builder->transaction->data['value']);
        $this->assertSame($fixture['data']['v'], $builder->transaction->data['v']);
        $this->assertSame($fixture['data']['r'], $builder->transaction->data['r']);
        $this->assertSame($fixture['data']['s'], $builder->transaction->data['s']);

        $this->assertSame($fixture['serialized'], $builder->transaction->serialize()->getHex());

        $this->assertSame($fixture['data']['id'], $builder->transaction->data['id']);

        $this->assertTrue($builder->verify());
    }

    /** @test */
    public function it_should_handle_large_amounts()
    {
        $fixture = $this->getTransactionFixture('evm_call', 'transfer-large-amount');

        $builder = TransferBuilder::new()
            ->gasPrice(UnitConverter::parseUnits($fixture['data']['gasPrice'], 'wei'))
            ->nonce($fixture['data']['nonce'])
            ->network($fixture['data']['network'])
            ->gasLimit(UnitConverter::parseUnits($fixture['data']['gasLimit'], 'wei'))
            ->recipientAddress($fixture['data']['recipientAddress'])
            ->value(UnitConverter::parseUnits($fixture['data']['value'], 'wei'))
            ->sign($this->passphrase);

        $this->assertSame((string) $fixture['data']['gasPrice'], (string) $builder->transaction->data['gasPrice']);
        $this->assertSame($fixture['data']['nonce'], $builder->transaction->data['nonce']);
        $this->assertSame($fixture['data']['network'], $builder->transaction->data['network']);
        $this->assertSame((string) $fixture['data']['gasLimit'], (string) $builder->transaction->data['gasLimit']);
        $this->assertSame($fixture['data']['recipientAddress'], $builder->transaction->data['recipientAddress']);
        $this->assertSame((string) $fixture['data']['value'], (string) $builder->transaction->data['value']);
        $this->assertSame($fixture['data']['v'], $builder->transaction->data['v']);
        $this->assertSame($fixture['data']['r'], $builder->transaction->data['r']);
        $this->assertSame($fixture['data']['s'], $builder->transaction->data['s']);

        $this->assertSame($fixture['serialized'], $builder->transaction->serialize()->getHex());

        $this->assertSame($fixture['data']['id'], $builder->transaction->data['id']);

        $this->assertTrue($builder->verify());
    }

    /** @test */
    public function it_should_handle_unit_converter()
    {
        $builder = TransferBuilder::new()
            ->gasPrice(UnitConverter::parseUnits(5, 'gwei'))
            ->nonce('1')
            ->gasLimit(UnitConverter::parseUnits(0.1, 'gwei'))
            ->recipientAddress($this->address)
            ->value(UnitConverter::parseUnits(10, 'ark'))
            ->sign($this->passphrase);

        $this->assertSame('5000000000', (string) $builder->transaction->data['gasPrice']);
        $this->assertSame('1', $builder->transaction->data['nonce']);
        $this->assertSame('100000000', (string) $builder->transaction->data['gasLimit']);
        $this->assertSame('10000000000000000000', (string) $builder->transaction->data['value']);

        $this->assertTrue($builder->verify());
    }
}
