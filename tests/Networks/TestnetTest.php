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

use ArkEcosystem\Crypto\Networks\Testnet as TestClass;
use ArkEcosystem\Tests\Crypto\TestCase;
use BitWasp\Bitcoin\Network\Network;

/**
 * This is the testnet network test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @coversNothing
 */
class TestnetTest extends TestCase
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
        $this->assertSame($actual, "TEST message:\n");
    }

    /** @test */
    public function it_should_get_nethash()
    {
        $actual = TestClass::getNethash();

        $this->assertInternalType('string', $actual);
        $this->assertSame($actual, 'd9acd04bde4234a81addb8482333b4ac906bed7be5a9970ce8ada428bd083192');
    }

    /** @test */
    public function it_should_get_wif()
    {
        $actual = TestClass::getWif();

        $this->assertInternalType('integer', $actual);
        $this->assertSame($actual, 186);
    }

    /** @test */
    public function it_should_get_wif_byte()
    {
        $actual = TestClass::getWifByte();

        $this->assertInternalType('string', $actual);
        $this->assertSame($actual, 'ba');
    }

    /** @test */
    public function it_should_get_factory()
    {
        $this->assertInstanceOf(Network::class, TestClass::getFactory());
    }
}
