<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Unit\Transactions\Builder;

use ArkEcosystem\Crypto\Identities\PublicKey;
use ArkEcosystem\Crypto\Transactions\Builder\VoteBuilder;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * @covers \ArkEcosystem\Crypto\Transactions\Builder\VoteBuilder
 */
class VoteTest extends TestCase
{
    /** @test */
    public function it_should_sign_it_with_a_passphrase()
    {
        $transaction = VoteBuilder::new()
            ->votes(['03f25455408f9a7e6c6a056b121e68fbda98f3511d22e9ef27b0ebaf1ef9e4eabc'])
            ->sign($this->passphrase);

        $this->assertTrue($transaction->verify());
    }

    /** @test */
    public function it_should_multi_sign()
    {
        $fixture = $this->getTransactionFixture('vote', 'vote-multi-sign');
        $builder = VoteBuilder::new()
            ->votes($fixture['data']['asset']['votes'])
            ->withNonce($fixture['data']['nonce'])
            ->withNetwork($fixture['data']['network']);

        foreach ($this->passphrases as $index => $passphrase) {
            $builder->multiSign($passphrase, $index);
        }

        $builder->sign($this->passphrase);

        $this->assertTrue($builder->verify());

        $this->assertSameSerializationMultisignature($fixture['serialized'], $builder->transaction->serialize()->getHex(), 3);

        $this->assertSameTransactions($fixture, $builder->transaction->data);
    }

    public function it_should_sign_it_with_a_second_passphrase()
    {
        $transaction = VoteBuilder::new()
            ->votes(['03f25455408f9a7e6c6a056b121e68fbda98f3511d22e9ef27b0ebaf1ef9e4eabc'])
            ->sign($this->passphrase)
            ->secondSign($this->secondPassphrase);

        $this->assertTrue($transaction->verify());
        $this->assertTrue($transaction->secondVerify(PublicKey::fromPassphrase($this->secondPassphrase)->getHex()));
    }

    /** @test */
    public function it_should_match_fixture_passphrase()
    {
        $fixture = $this->getTransactionFixture('vote', 'vote-sign');
        $builder = VoteBuilder::new()
            ->votes($fixture['data']['asset']['votes'])
            ->withNonce($fixture['data']['nonce'])
            ->withNetwork($fixture['data']['network'])
            ->sign($this->passphrase);

        $this->assertTrue($builder->verify());
        $this->assertSameSerialization($fixture['serialized'], $builder->transaction->serialize()->getHex());

        $this->assertSameTransactions($fixture, $builder->transaction->data);
    }
}
