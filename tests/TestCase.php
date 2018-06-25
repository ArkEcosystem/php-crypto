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

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Get a transaction fixture by type.
     *
     * @param int $type
     *
     * @return object
     */
    protected function getTransactionFixture(int $type): object
    {
        $path = __DIR__."/fixtures/Transactions/type-{$type}.json";

        return json_decode(file_get_contents($path));
    }
}
