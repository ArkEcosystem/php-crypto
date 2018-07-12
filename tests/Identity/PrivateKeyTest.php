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

use ArkEcosystem\Crypto\Identity\PrivateKey as TestClass;
use ArkEcosystem\Crypto\Networks\Devnet;
use ArkEcosystem\Tests\Crypto\TestCase;
use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PrivateKey as EcPublicKey;

/**
 * This is the address test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @coversNothing
 */
class PrivateKeyTest extends TestCase
{
    /** @test */
    public function it_should_get_the_private_key_from_passphrase()
    {
        $fixture = $this->getIdentityFixtures();

        $actual = TestClass::fromPassphrase($fixture->passphrase, Devnet::new());

        $this->assertInstanceOf(EcPublicKey::class, $actual);
        $this->assertSame($fixture->data->privateKey, $actual->getHex());
    }

    /** @test */
    public function it_should_get_the_private_key_from_hex()
    {
        $fixture = $this->getIdentityFixtures();

        $actual = TestClass::fromHex($fixture->data->privateKey);

        $this->assertSame($fixture->data->privateKey, $actual->getHex());
    }

    /** @test */
    public function it_should_get_the_private_key_from_wif()
    {
        $fixture = $this->getIdentityFixtures();

        $actual = TestClass::fromWif($fixture->data->wif, Devnet::new());

        $this->assertSame($fixture->data->privateKey, $actual->getHex());
    }
}
