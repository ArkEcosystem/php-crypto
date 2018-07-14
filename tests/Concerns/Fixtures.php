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
     * Get a transaction fixture by type and name.
     *
     * @param int    $type
     * @param string $name
     *
     * @return array|object
     */
    protected function getTransactionFixture(int $type, string $name): array
    {
        $path = __DIR__."/../fixtures/Transactions/type-{$type}/{$name}.json";

        return json_decode(file_get_contents($path), true);
    }

    /**
     * Get a transaction fixture by type with a passphrase.
     *
     * @param int $type
     *
     * @return array|object
     */
    protected function getTransactionFixtureWithPassphrase(int $type): array
    {
        return $this->getTransactionFixture($type, 'passphrase');
    }

    /**
     * Get a transaction fixture by type with a second passphrase.
     *
     * @param int $type
     *
     * @return array|object
     */
    protected function getTransactionFixtureWithSecondPassphrase(int $type): array
    {
        return $this->getTransactionFixture($type, 'second-passphrase');
    }

    /**
     * Get the identity fixture.
     *
     * @return array
     */
    protected function getIdentityFixtures(): array
    {
        $path = __DIR__.'/../fixtures/identity.json';

        return json_decode(file_get_contents($path), true);
    }
}
