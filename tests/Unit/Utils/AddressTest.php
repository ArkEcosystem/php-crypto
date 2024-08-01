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

namespace ArkEcosystem\Tests\Crypto\Unit\Utils;

use ArkEcosystem\Crypto\Utils\Address as TestClass;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
  * @covers \ArkEcosystem\Crypto\Identities\Address
  */
class AddressTest extends TestCase
{
    /** @test */
    public function it_should_validate_the_address()
    {
        $fixture = $this->getFixture('identity');

        $actual = TestClass::validate($fixture['data']['address']);

        $this->assertTrue($actual);
    }

    /** @test */
    public function it_should_fail_to_validate_the_address()
    {
        $actual = TestClass::validate('invalid');

        $this->assertFalse($actual);
    }
}
