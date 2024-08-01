<?php

declare(strict_types=1);



namespace ArkEcosystem\Tests\Crypto\Unit\Utils;

use ArkEcosystem\Crypto\Utils\Slot;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
  * @covers \ArkEcosystem\Crypto\Utils\Slot
  */
class SlotTest extends TestCase
{
    /** @test */
    public function it_should_get_the_time()
    {
        $actual = Slot::time();

        $this->assertIsInt($actual);
    }

    /** @test */
    public function it_should_get_the_epoch()
    {
        $actual = Slot::epoch();

        $this->assertIsInt($actual);
    }
}
