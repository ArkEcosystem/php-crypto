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

namespace ArkEcosystem\Tests\Crypto\Concerns;

trait Fixtures
{
    /**
     * Get a fixture.
     *
     * @param string $file
     *
     * @return array
     */
    protected function getFixture(string $file): array
    {
        $path = __DIR__."/../fixtures/{$file}.json";

        return json_decode(file_get_contents($path), true);
    }

    /**
     * Get a transaction fixture by type and name.
     *
     * @param string $type
     * @param string $name
     *
     * @return array|object
     */
    protected function getTransactionFixture(string $type, string $name): array
    {
        return $this->getFixture("transactions/{$type}/{$name}");
    }
}
