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
    public function it_should_get_the_address_from_public_key()
    {
        $actual = TestClass::fromPublicKey('034151a3ec46b5670a682b0a63394f863587d1bc97483b1b6c70eb58e7f0aed192', Devnet::new());

        $this->assertSame('D61mfSggzbvQgTUe6JhYKH2doHaqJ3Dyib', $actual);
    }

    /** @test */
    public function it_should_get_the_address_from_passphrase()
    {
        $actual = TestClass::fromPassphrase('this is a top secret passphrase', Devnet::new());

        $this->assertSame('D61mfSggzbvQgTUe6JhYKH2doHaqJ3Dyib', $actual);
    }

    /** @test */
    public function it_should_get_the_address_from_private_key()
    {
        $privateKey = PrivateKey::fromPassphrase('this is a top secret passphrase');

        $actual = TestClass::fromPrivateKey($privateKey, Devnet::new());

        $this->assertSame('D61mfSggzbvQgTUe6JhYKH2doHaqJ3Dyib', $actual);
    }

    /** @test */
    public function it_should_validate_the_address()
    {
        $actual = TestClass::validate('D61mfSggzbvQgTUe6JhYKH2doHaqJ3Dyib', Devnet::new());

        $this->assertInternalType('boolean', $actual);
        $this->assertTrue($actual);
    }
}
