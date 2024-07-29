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

use ArkEcosystem\Crypto\Transactions\Builder\MultiSignatureRegistrationBuilder;
use ArkEcosystem\Crypto\Transactions\Serializer;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * This is the multi signature registration builder test class.
 *
 * @author Brian Faust <brian@ark.io>
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

        $serialized = Serializer::new($builder->transaction)->serialize()->getHex();
        $this->assertTrue($builder->verify());
        $this->assertSameSerializationMultisignature($fixture['serialized'], $serialized, 3);
        $this->assertSignaturesAreSerialized($serialized, $builder->transaction->data['signatures']);
        $this->assertSameTransactions($fixture, $builder->transaction->data);
    }
}
