<?php

declare(strict_types=1);



namespace ArkEcosystem\Tests\Crypto\Unit\Identities;

use ArkEcosystem\Crypto\Identities\WIF as TestClass;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
  * @covers \ArkEcosystem\Crypto\Identities\WIF
  */
class WIFTest extends TestCase
{
    /** @test */
    public function it_should_get_the_wif_from_passphrase()
    {
        $fixture = $this->getFixture('identity');

        $actual = TestClass::fromPassphrase($fixture['passphrase']);

        $this->assertSame($fixture['data']['wif'], $actual);
    }
}
