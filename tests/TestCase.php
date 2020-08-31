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

namespace ArkEcosystem\Tests\Crypto;

use ArkEcosystem\Crypto\Configuration\Network;
use ArkEcosystem\Crypto\Networks\Mainnet;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use Concerns\Fixtures,
        Concerns\Serialize,
        Concerns\Deserialize;

    protected $passphrase = 'this is a top secret passphrase';

    protected $secondPassphrase = 'this is a top secret second passphrase';

    protected function setUp(): void
    {
        Network::set(Mainnet::new());
    }
}
