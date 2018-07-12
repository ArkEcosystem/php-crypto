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
use ArkEcosystem\Crypto\Networks\Devnet;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp()
    {
        Network::set(Devnet::new());
    }

    /**
     * Get a transaction fixture by type.
     *
     * @param int $type
     *
     * @return object
     */
    protected function getTransactionFixtureWithPassphrase(int $type): object
    {
        $path = __DIR__."/fixtures/Transactions/type-{$type}/passphrase.json";

        return json_decode(file_get_contents($path));
    }

    /**
     * Get a transaction fixture by type.
     *
     * @param int $type
     *
     * @return object
     */
    protected function getTransactionFixtureWithSecondPassphrase(int $type): object
    {
        $path = __DIR__."/fixtures/Transactions/type-{$type}/second-passphrase.json";

        return json_decode(file_get_contents($path));
    }

    /**
     * Get the identity fixture.
     *
     * @return object
     */
    protected function getIdentityFixtures(): object
    {
        $path = __DIR__.'/fixtures/identity.json';

        return json_decode(file_get_contents($path));
    }
}
