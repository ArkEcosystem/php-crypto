<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Unit\Transactions\Builder;

use ArkEcosystem\Crypto\Enums\ContractAbiType;
use ArkEcosystem\Crypto\Identities\PrivateKey;
use ArkEcosystem\Crypto\Transactions\Builder\MultipaymentBuilder;
use ArkEcosystem\Crypto\Transactions\Types\Multipayment;
use ArkEcosystem\Crypto\Utils\AbiEncoder;
use ArkEcosystem\Crypto\Utils\UnitConverter;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * @covers \ArkEcosystem\Crypto\Transactions\Builder\MultipaymentBuilder
 */
class MultipaymentBuilderTest extends TestCase
{
    /** @test */
    public function it_should_sign_it_with_a_passphrase()
    {
        $fixture = $this->getTransactionFixture('evm_call', 'multipayment');

        $builder = MultipaymentBuilder::new()
            ->gasPrice(UnitConverter::parseUnits($fixture['data']['gasPrice'], 'wei'))
            ->nonce($fixture['data']['nonce'])
            ->network($fixture['data']['network'])
            ->gasLimit(UnitConverter::parseUnits($fixture['data']['gasLimit'], 'wei'))
            ->pay('0x6f0182a0cc707b055322ccf6d4cb6a5aff1aeb22', UnitConverter::parseUnits('100000', 'wei'))
            ->pay('0xc3bbe9b1cee1ff85ad72b87414b0e9b7f2366763', UnitConverter::parseUnits('200000', 'wei'))
            ->sign($this->passphrase);

        $this->assertSame((string) $fixture['data']['gasPrice'], (string) $builder->transaction->data['gasPrice']);
        $this->assertSame($fixture['data']['nonce'], $builder->transaction->data['nonce']);
        $this->assertSame($fixture['data']['network'], $builder->transaction->data['network']);
        $this->assertSame((string) $fixture['data']['gasLimit'], (string) $builder->transaction->data['gasLimit']);
        $this->assertSame($fixture['data']['v'], $builder->transaction->data['v']);
        $this->assertSame($fixture['data']['r'], $builder->transaction->data['r']);
        $this->assertSame($fixture['data']['s'], $builder->transaction->data['s']);

        $this->assertSame($fixture['serialized'], $builder->transaction->serialize()->getHex());

        $this->assertSame($fixture['data']['id'], $builder->transaction->data['id']);

        $this->assertTrue($builder->verify());
    }

    /** @test */
    public function it_should_handle_single_recipient()
    {
        $fixture = $this->getTransactionFixture('evm_call', 'multipayment-single');

        $builder = MultipaymentBuilder::new()
            ->gasPrice(UnitConverter::parseUnits($fixture['data']['gasPrice'], 'wei'))
            ->nonce($fixture['data']['nonce'])
            ->network($fixture['data']['network'])
            ->gasLimit(UnitConverter::parseUnits($fixture['data']['gasLimit'], 'wei'))
            ->pay('0x6f0182a0cc707b055322ccf6d4cb6a5aff1aeb22', UnitConverter::parseUnits('100000', 'wei'))
            ->sign($this->passphrase);

        $this->assertSame((string) $fixture['data']['gasPrice'], (string) $builder->transaction->data['gasPrice']);
        $this->assertSame($fixture['data']['nonce'], $builder->transaction->data['nonce']);
        $this->assertSame($fixture['data']['network'], $builder->transaction->data['network']);
        $this->assertSame((string) $fixture['data']['gasLimit'], (string) $builder->transaction->data['gasLimit']);
        $this->assertSame($fixture['data']['v'], $builder->transaction->data['v']);
        $this->assertSame($fixture['data']['r'], $builder->transaction->data['r']);
        $this->assertSame($fixture['data']['s'], $builder->transaction->data['s']);

        $this->assertSame($fixture['serialized'], $builder->transaction->serialize()->getHex());

        $this->assertSame($fixture['data']['id'], $builder->transaction->data['id']);

        $this->assertTrue($builder->verify());
    }

    /** @test */
    public function it_should_handle_empty_payment()
    {
        $fixture = $this->getTransactionFixture('evm_call', 'multipayment-empty');

        $builder = MultipaymentBuilder::new()
            ->gasPrice(UnitConverter::parseUnits($fixture['data']['gasPrice'], 'wei'))
            ->nonce($fixture['data']['nonce'])
            ->network($fixture['data']['network'])
            ->gasLimit(UnitConverter::parseUnits($fixture['data']['gasLimit'], 'wei'))
            ->sign($this->passphrase);

        $this->assertSame((string) $fixture['data']['gasPrice'], (string) $builder->transaction->data['gasPrice']);
        $this->assertSame($fixture['data']['nonce'], $builder->transaction->data['nonce']);
        $this->assertSame($fixture['data']['network'], $builder->transaction->data['network']);
        $this->assertSame((string) $fixture['data']['gasLimit'], (string) $builder->transaction->data['gasLimit']);
        $this->assertSame($fixture['data']['v'], $builder->transaction->data['v']);
        $this->assertSame($fixture['data']['r'], $builder->transaction->data['r']);
        $this->assertSame($fixture['data']['s'], $builder->transaction->data['s']);

        $this->assertSame($fixture['serialized'], $builder->transaction->serialize()->getHex());

        $this->assertSame($fixture['data']['id'], $builder->transaction->data['id']);

        $this->assertTrue($builder->verify());
    }

    // TODO: fix decoder issue first
    // /** @test */
    // public function it_should_be_possible_to_create_manual_multipayment()
    // {
    //     $fixture = $this->getTransactionFixture('evm_call', 'multipayment-1');

    //     $payload = (new AbiEncoder(ContractAbiType::MULTIPAYMENT))->encodeFunctionCall('pay', [['0x8233F6Df6449D7655f4643D2E752DC8D2283fAd5'], ['1000000000000000000']]);
    //     $tx = (new Multipayment(['data' => $payload]));
    //     $tx->data['nonce'] = $fixture['data']['nonce'];
    //     $tx->data['network'] = $fixture['data']['network'];
    //     $tx->data['gasLimit'] = $fixture['data']['gasLimit'];
    //     $tx->data['gasPrice'] = $fixture['data']['gasPrice'];
    //     $tx->sign(PrivateKey::fromPassphrase($this->passphrase));

    //     $this->assertTrue($tx->verify());
    // }
}
