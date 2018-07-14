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

use ArkEcosystem\Crypto\Identity\Address as TestClass;
use ArkEcosystem\Crypto\Identity\PrivateKey;
use ArkEcosystem\Crypto\Networks\Devnet;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * This is the address test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @coversNothing
 */
class AddressTest extends TestCase
{
    /** @test */
    public function it_should_get_the_address_from_passphrase()
    {
        $fixture = $this->getIdentityFixtures();

        $actual = TestClass::fromPassphrase($fixture['passphrase'], Devnet::new());

        $this->assertSame($fixture['data']['address'], $actual);
    }

    /** @test */
    public function it_should_get_the_address_from_public_key()
    {
        $fixture = $this->getIdentityFixtures();

        $actual = TestClass::fromPublicKey($fixture['data']['publicKey'], Devnet::new());

        $this->assertSame($fixture['data']['address'], $actual);
    }

    /** @test */
    public function it_should_get_the_address_from_private_key()
    {
        $fixture = $this->getIdentityFixtures();

        $privateKey = PrivateKey::fromPassphrase($fixture['passphrase']);

        $actual = TestClass::fromPrivateKey($privateKey, Devnet::new());

        $this->assertSame($fixture['data']['address'], $actual);
    }

    /** @test */
    public function it_should_validate_the_address()
    {
        $fixture = $this->getIdentityFixtures();

        $actual = TestClass::validate($fixture['data']['address'], Devnet::new());

        $this->assertTrue($actual);
    }
}
