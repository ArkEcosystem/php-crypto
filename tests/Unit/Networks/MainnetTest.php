<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Unit\Networks;

use ArkEcosystem\Crypto\Networks\Mainnet;

/**
 * @covers \ArkEcosystem\Crypto\Networks\Mainnet
 */
class MainnetTest extends NetworkTestCase
{
    protected $epoch = '2017-03-21T13:00:00.000Z';

    protected $pubKeyHash = 23;

    public function getTestSubject()
    {
        return Mainnet::new();
    }
}
