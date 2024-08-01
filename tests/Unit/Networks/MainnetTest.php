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

namespace ArkEcosystem\Tests\Crypto\Unit\Networks;

use ArkEcosystem\Crypto\Networks\Mainnet;
use BitWasp\Bitcoin\Network\Network;

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
