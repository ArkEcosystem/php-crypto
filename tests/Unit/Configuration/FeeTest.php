<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Unit\Managers;

use ArkEcosystem\Crypto\Configuration\Fee;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * @covers \ArkEcosystem\Crypto\Configuration\Fee
 */
class FeeTest extends TestCase
{
    /** @test */
    public function it_should_get_the_fee()
    {
        $actual = Fee::get(0);

        $this->assertSame('10000000', $actual);
    }

    /** @test */
    public function it_should_set_the_fee()
    {
        $actual = Fee::get(0);
        $this->assertSame('10000000', $actual);

        Fee::set(0, '5');

        $actual = Fee::get(0);
        $this->assertSame('5', $actual);
    }
}
