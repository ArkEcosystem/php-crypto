<?php

declare(strict_types=1);

/*
 * This file is part of Ark PHP Crypto.
 *
 * (c) Ark Ecosystem <info@ark.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ArkEcosystem\Tests\Crypto\Unit\Transactions\Builder;

use ArkEcosystem\Crypto\Transactions\Builder\ValidatorResignationBuilder;
use ArkEcosystem\Crypto\Transactions\Serializer;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * This is the delegate resignation builder test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @covers \ArkEcosystem\Crypto\Transactions\Builder\ValidatorResignationBuilder
 */
class ValidatorResignationTest extends TestCase
{
    /** @test */
    public function it_should_sign_it_with_a_passphrase()
    {
        $transaction = ValidatorResignationBuilder::new()
            ->sign($this->passphrase);

        $this->assertTrue($transaction->verify());
    }

    /** @test */
    public function it_should_multi_sign()
    {
        $fixture = $this->getTransactionFixture('validator_resignation', 'validator-resignation-multi-sign');
        $builder = ValidatorResignationBuilder::new()
            ->withFee($fixture['data']['fee'])
            ->withNonce($fixture['data']['nonce'])
            ->withNetwork($fixture['data']['network']);

        foreach ($this->passphrases as $index => $passphrase) {
            $builder->multiSign($passphrase, $index);
        }

        $builder->sign($this->passphrase);

        $this->assertTrue($builder->verify());

        $this->assertSameSerializationMultisignature($fixture['serialized'], Serializer::new($builder->transaction)->serialize()->getHex(), 3);

        $this->assertSameTransactions($fixture, $builder->transaction->data);
    }

    /** @test */
    public function it_should_match_fixture_passphrase()
    {
        $fixture = $this->getTransactionFixture('validator_resignation', 'validator-resignation-sign');
        $builder = ValidatorResignationBuilder::new()
            ->withFee($fixture['data']['fee'])
            ->withNonce($fixture['data']['nonce'])
            ->withNetwork($fixture['data']['network'])
            ->sign($this->passphrase);

        $this->assertTrue($builder->verify());
        $this->assertSameSerialization($fixture['serialized'], Serializer::new($builder->transaction)->serialize()->getHex());
        $this->assertSameTransactions($fixture, $builder->transaction->data);
    }
}
