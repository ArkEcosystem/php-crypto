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

namespace ArkEcosystem\Tests\Crypto\Identity;

use ArkEcosystem\Crypto\Identity\PublicKey as TestClass;
use ArkEcosystem\Tests\Crypto\TestCase;
use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PublicKey as EcPublicKey;

/**
 * This is the address test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @coversNothing
 */
class PublicKeyTest extends TestCase
{
    /** @test */
    public function it_should_get_the_public_key_from_passphrase()
    {
        $fixture = $this->getFixture('identity');

        $actual = TestClass::fromPassphrase($fixture['passphrase']);

        $this->assertInstanceOf(EcPublicKey::class, $actual);
        $this->assertSame($fixture['data']['publicKey'], $actual->getHex());
    }

    /** @test */
    public function it_should_get_the_public_key_from_hex()
    {
        $fixture = $this->getFixture('identity');

        $actual = TestClass::fromHex($fixture['data']['publicKey']);

        $this->assertSame($fixture['data']['publicKey'], $actual->getHex());
    }

    /** @test */
    public function it_should_validate_the_public_key()
    {
        $fixture = $this->getFixture('identity');

        $actual = TestClass::validate($fixture['data']['publicKey']);

        $this->assertTrue($actual);
    }
}
