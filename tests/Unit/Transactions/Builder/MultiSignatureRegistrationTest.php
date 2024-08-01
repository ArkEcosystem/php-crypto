<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Unit\Transactions\Builder;

use ArkEcosystem\Crypto\Identities\PublicKey;
use ArkEcosystem\Crypto\Transactions\Builder\MultiSignatureRegistrationBuilder;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * @covers \ArkEcosystem\Crypto\Transactions\Builder\MultiSignatureRegistrationBuilder
 */
class MultiSignatureRegistrationTest extends TestCase
{
    /** @test */
    public function it_should_sign_it_with_a_passphrase()
    {
        $fixture = $this->getTransactionFixture('multi_signature_registration', 'multi-signature-registration-sign');

        $transaction = MultiSignatureRegistrationBuilder::new()
            ->multiSignatureAsset([
                'min'        => $fixture['data']['asset']['multiSignature']['min'],
                'publicKeys' => $fixture['data']['asset']['multiSignature']['publicKeys'],
            ])
            ->sign('secret');

        $this->assertTrue($transaction->verify());
    }

    /** @test */
    public function it_should_sign_it_with_a_second_passphrase()
    {
        $fixture = $this->getTransactionFixture('multi_signature_registration', 'multi-signature-registration-sign');

        $transaction = MultiSignatureRegistrationBuilder::new()
            ->multiSignatureAsset([
                'min'        => $fixture['data']['asset']['multiSignature']['min'],
                'publicKeys' => $fixture['data']['asset']['multiSignature']['publicKeys'],
            ])
            ->sign('secret')
            ->secondSign($this->secondPassphrase);

        $this->assertTrue($transaction->verify());
        $this->assertTrue($transaction->secondVerify(PublicKey::fromPassphrase($this->secondPassphrase)->getHex()));
    }

    /** @test */
    public function it_should_match_fixture_passphrase()
    {
        $fixture = $this->getTransactionFixture('multi_signature_registration', 'multi-signature-registration-sign');

        $builder = MultiSignatureRegistrationBuilder::new()
            ->withFee($fixture['data']['fee'])
            ->withNonce($fixture['data']['nonce'])
            ->withNetwork($fixture['data']['network'])
            ->multiSignatureAsset([
                'min'        => $fixture['data']['asset']['multiSignature']['min'],
                'publicKeys' => $fixture['data']['asset']['multiSignature']['publicKeys'],
            ]);

        foreach ($this->passphrases as $index => $passphrase) {
            $builder->multiSign($passphrase, $index);
        }

        $builder->sign($this->passphrase);

        $serialized = $builder->transaction->serialize()->getHex();
        $this->assertTrue($builder->verify());
        $this->assertSameSerializationMultisignature($fixture['serialized'], $serialized, 3);
        $this->assertSignaturesAreSerialized($serialized, $builder->transaction->data['signatures']);
        $this->assertSameTransactions($fixture, $builder->transaction->data);
    }
}
