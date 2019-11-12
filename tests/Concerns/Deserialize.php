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


use Illuminate\Support\Arr;
use ArkEcosystem\Crypto\Transactions\Deserializer;
use ArkEcosystem\Crypto\Transactions\Serializer;

trait Deserialize
{
    protected function assertDeserialized(array $expected, array $keys, int $network = 30): object
    {
        $actual = Deserializer::new($expected['serialized'])->deserialize();
        $data = $actual->data;

        $this->assertSame($expected['serialized'], Serializer::new($actual)->serialize()->getHex());
        $this->assertSameTransactions($expected, $data, $keys);
        $this->assertTrue($actual->verify());

        return $actual;
    }

    protected function object_to_array(object $value): array
    {
        return json_decode(json_encode($value), true);
    }

    private function array_only(array $arr, array $keys): array
    {
        $returnArray = [];
        foreach ($keys as $key) {
            if (isset($arr[$key])) {
                $returnArray[$key] = $arr[$key];
            }
        }

        return $returnArray;
    }

    protected function assertSameTransactions(array $expected, array $actual, array $keys): void
    {
        $expected = Arr::only($expected['data'], $keys);
        $actual = Arr::only($actual, $keys);

        ksort($expected);
        ksort($actual);

        if (isset($actual['asset']['multiSignature'])) {
            ksort($expected['asset']['multiSignature']);
            ksort($actual['asset']['multiSignature']);
        } elseif (isset($actual['asset']['multiSignatureLegacy'])) {
            ksort($expected['asset']['multiSignatureLegacy']);
            ksort($actual['asset']['multiSignatureLegacy']);
        }

        $this->assertSame($expected, $actual);
    }
}
