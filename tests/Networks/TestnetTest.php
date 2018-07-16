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

namespace ArkEcosystem\Tests\Crypto\Networks;

use ArkEcosystem\Crypto\Networks\Testnet;
use BitWasp\Bitcoin\Network\Network;

/**
 * This is the testnet network test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @covers \ArkEcosystem\Crypto\Networks\Testnet
 */
class TestnetTest extends NetworkTestCase
{
    protected $version = 23;
    protected $epoch   = '2017-03-21T13:00:00.000Z';
    protected $nethash = 'd9acd04bde4234a81addb8482333b4ac906bed7be5a9970ce8ada428bd083192';
    protected $wif     = 186;
    protected $wifByte = 'ba';

    public function getTestSubject()
    {
        return Testnet::class;
    }
}
