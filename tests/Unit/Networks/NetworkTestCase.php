<?php

declare(strict_types=1);



namespace ArkEcosystem\Tests\Crypto\Unit\Networks;

use ArkEcosystem\Tests\Crypto\TestCase;


class NetworkTestCase extends TestCase
{
    /** @test */
    public function it_should_get_epoch()
    {
        $actual = $this->getTestSubject()->epoch();

        $this->assertSame($actual, $this->epoch);
    }

    /** @test */
    public function it_should_get_public_key_hash()
    {
        $actual = $this->getTestSubject()->pubKeyHash();

        $this->assertSame($actual, $this->pubKeyHash);
    }
}
