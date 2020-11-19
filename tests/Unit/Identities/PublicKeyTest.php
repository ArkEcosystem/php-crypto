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

namespace ArkEcosystem\Tests\Crypto\Unit\Identities;

use ArkEcosystem\Crypto\Identities\PublicKey as TestClass;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * This is the address test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @covers \ArkEcosystem\Crypto\Identities\PublicKey
 */
class PublicKeyTest extends TestCase
{
    /** @test */
    public function it_should_get_the_public_key_from_passphrase()
    {
        $fixture = $this->getFixture('identity');

        $actual = TestClass::fromPassphrase($fixture['passphrase']);

        $this->assertSame($fixture['data']['publicKey'], $actual->getHex());
    }

    /** @test */
    public function it_should_get_the_public_key_from_a_multi_signature_asset()
    {
        $actual = TestClass::fromMultiSignatureAsset(3, [
            TestClass::fromPassphrase('secret 1'),
            TestClass::fromPassphrase('secret 2'),
            TestClass::fromPassphrase('secret 3'),
        ]);

        $this->assertSame(
            '0279f05076556da7173610a7676399c3620276ebbf8c67552ad3b1f26ec7627794',
            $actual->getHex()
        );
    }

    /** @test */
    public function it_should_get_the_public_key_from_hex()
    {
        $fixture = $this->getFixture('identity');

        $actual = TestClass::fromHex($fixture['data']['publicKey']);

        $this->assertSame($fixture['data']['publicKey'], $actual->getHex());
    }
}
