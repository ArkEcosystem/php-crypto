<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Concerns;

use ArkEcosystem\Crypto\Transactions\Deserializer;
use Illuminate\Support\Arr;

trait Deserialize
{
    protected function assertDeserialized(array $expected, array $keys): object
    {
        $actual = Deserializer::new($expected['serialized'])->deserialize();

        $data   = $actual->data;

        $this->assertSame($expected['serialized'], $actual->serialize()->getHex());
        $this->assertSameTransactions($expected, $data, $keys);

        return $actual;
    }

    protected function object_to_array(object $value): array
    {
        return json_decode(json_encode($value), true);
    }

    protected function assertSameTransactions(array $expected, array $actual, array $keys = []): void
    {
        if (empty($keys)) {
            $keys = array_keys($expected['data']);
        }

        if (in_array('to', $keys, true)) {
            array_splice($keys, array_search('to', $keys, true), 1);

            $this->assertArrayHasKey('to', $expected['data']);
            $this->assertSame(strtolower($expected['data']['to']), strtolower($actual['to']));
        }

        $expected = Arr::only($expected['data'], $keys);
        $actual   = Arr::only($actual, $keys);

        ksort($expected);
        ksort($actual);

        $this->assertSame($expected, $actual);
    }
}
