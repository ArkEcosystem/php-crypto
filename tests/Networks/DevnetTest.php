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

use ArkEcosystem\Crypto\Networks\Devnet;
use BitWasp\Bitcoin\Network\Network;

/**
 * This is the devnet network test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @coversNothing
 */
class DevnetTest extends NetworkTestCase
{
    protected $version = 30;
    protected $nethash = '578e820911f24e039733b45e4882b73e301f813a0d2c31330dafda84534ffa23';
    protected $wif     = 170;
    protected $wifByte = 'aa';

    public function getTestSubject()
    {
        return Devnet::class;
    }
}
