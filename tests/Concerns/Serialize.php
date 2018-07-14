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

use ArkEcosystem\Crypto\Serializer;

trait Serialize
{
    protected function assertSerialized(array $fixture): void
    {
        $actual = Serializer::new($fixture['data'])->serialize();

        $this->assertSame($fixture['serialized'], $actual->getHex());
    }
}
