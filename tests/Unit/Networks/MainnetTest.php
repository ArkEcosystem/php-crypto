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

    /**
     * @todo: adjust the $chainId once known
     */
    protected $chainId = 10000;

    public function getTestSubject()
    {
        return Mainnet::new();
    }
}
