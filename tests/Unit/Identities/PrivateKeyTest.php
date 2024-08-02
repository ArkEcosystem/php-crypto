<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Unit\Identities;

use ArkEcosystem\Crypto\Identities\PrivateKey as TestClass;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * @covers \ArkEcosystem\Crypto\Identities\PrivateKey
 */
class PrivateKeyTest extends TestCase
{
    /** @test */
    public function it_should_get_the_private_key_from_passphrase()
    {
        $fixture = $this->getFixture('identity');

        $actual = TestClass::fromPassphrase($fixture['passphrase']);

        $this->assertSame($fixture['data']['privateKey'], $actual->getHex());
    }

    /** @test */
    public function it_should_get_the_private_key_from_hex()
    {
        $fixture = $this->getFixture('identity');

        $actual = TestClass::fromHex($fixture['data']['privateKey']);

        $this->assertSame($fixture['data']['privateKey'], $actual->getHex());
    }

    /** @test */
    public function it_should_get_the_private_key_from_wif()
    {
        $fixture = $this->getFixture('identity');

        $actual = TestClass::fromWif($fixture['data']['wif']);

        $this->assertSame($fixture['data']['privateKey'], $actual->getHex());
    }
}
