<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Unit\Networks;

use ArkEcosystem\Crypto\Networks\Devnet;

/**
 * @covers \ArkEcosystem\Crypto\Networks\Devnet
 */
class DevnetTest extends NetworkTestCase
{
    protected $epoch = '2017-03-21T13:00:00.000Z';

    protected $pubKeyHash = 30;

    public function getTestSubject()
    {
        return Devnet::new();
    }
}
