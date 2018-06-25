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

namespace ArkEcosystem\Tests\Crypto\Networks;

use ArkEcosystem\Crypto\Networks\Mainnet as TestClass;
use ArkEcosystem\Tests\Crypto\TestCase;
use BitWasp\Bitcoin\Network\Network;

/**
 * This is the mainnet network test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @coversNothing
 */
class MainnetTest extends TestCase
{
    /** @test */
    public function it_should_get_version()
    {
        $actual = TestClass::getVersion();

        $this->assertInternalType('integer', $actual);
        $this->assertSame($actual, 23);
    }

    /** @test */
    public function it_should_get_message_prefix()
    {
        $actual = TestClass::getMessagePrefix();

        $this->assertInternalType('string', $actual);
        $this->assertSame($actual, "ARK message:\n");
    }

    /** @test */
    public function it_should_get_nethash()
    {
        $actual = TestClass::getNethash();

        $this->assertInternalType('string', $actual);
        $this->assertSame($actual, '6e84d08bd299ed97c212c886c98a57e36545c8f5d645ca7eeae63a8bd62d8988');
    }

    /** @test */
    public function it_should_get_wif()
    {
        $actual = TestClass::getWif();

        $this->assertInternalType('integer', $actual);
        $this->assertSame($actual, 170);
    }

    /** @test */
    public function it_should_get_wif_byte()
    {
        $actual = TestClass::getWifByte();

        $this->assertInternalType('string', $actual);
        $this->assertSame($actual, 'aa');
    }

    /** @test */
    public function it_should_get_factory()
    {
        $this->assertInstanceOf(Network::class, TestClass::getFactory());
    }
}
