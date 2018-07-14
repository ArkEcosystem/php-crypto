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

use ArkEcosystem\Crypto\Networks\Mainnet;
use BitWasp\Bitcoin\Network\Network;

/**
 * This is the mainnet network test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @coversNothing
 */
class MainnetTest extends NetworkTestCase
{
    protected $version = 23;
    protected $nethash = '6e84d08bd299ed97c212c886c98a57e36545c8f5d645ca7eeae63a8bd62d8988';
    protected $wif     = 170;
    protected $wifByte = 'aa';

    public function getTestSubject()
    {
        return Mainnet::class;
    }
}
