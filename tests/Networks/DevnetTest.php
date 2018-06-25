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

use ArkEcosystem\Crypto\Networks\Devnet as TestClass;
use ArkEcosystem\Tests\Crypto\TestCase;
use BitWasp\Bitcoin\Network\Network;

/**
 * This is the devnet network test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @coversNothing
 */
class DevnetTest extends TestCase
{
    /** @test */
    public function it_should_get_version()
    {
        $actual = TestClass::getVersion();

        $this->assertInternalType('integer', $actual);
        $this->assertSame($actual, 30);
    }

    /** @test */
    public function it_should_get_message_prefix()
    {
        $actual = TestClass::getMessagePrefix();

        $this->assertInternalType('string', $actual);
        $this->assertSame($actual, "DARK message:\n");
    }

    /** @test */
    public function it_should_get_nethash()
    {
        $actual = TestClass::getNethash();

        $this->assertInternalType('string', $actual);
        $this->assertSame($actual, '578e820911f24e039733b45e4882b73e301f813a0d2c31330dafda84534ffa23');
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
