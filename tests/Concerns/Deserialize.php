<?php

declare(strict_types=1);



namespace ArkEcosystem\Tests\Crypto\Concerns;

use ArkEcosystem\Crypto\Transactions\Deserializer;
use Illuminate\Support\Arr;

trait Deserialize
{
    protected function assertDeserialized(array $expected, array $keys, int $network = 30): object
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

        $expected = Arr::only($expected['data'], $keys);
        $actual   = Arr::only($actual, $keys);

        ksort($expected);
        ksort($actual);

        if (isset($actual['asset']['multiSignature'])) {
            ksort($expected['asset']['multiSignature']);
            ksort($actual['asset']['multiSignature']);
        } elseif (isset($actual['asset']['multiSignatureLegacy'])) {
            ksort($expected['asset']['multiSignatureLegacy']);
            ksort($actual['asset']['multiSignatureLegacy']);
        }

        if (isset($actual['asset']['payments'])) {
            for ($i = 0; $i < count($actual['asset']['payments']); $i++) {
                ksort($actual['asset']['payments'][$i]);
            }
        }

        // Signatures and IDs are not deterministic
        unset($expected['id']);
        unset($expected['signature']);
        unset($expected['signatures']);

        unset($actual['id']);
        unset($actual['signature']);
        unset($actual['signatures']);

        $this->assertSame($expected, $actual);
    }

    protected function assertSameSerialization(string $expected, string $actual): void
    {
        // Signatures is not deterministic so we need to remove them from the comparison
        $this->assertSame(substr($expected, 0, -128), substr($actual, 0, -128));
    }

    protected function assertSameSerializationMultisignature(string $expected, string $actual, int $numberOfParticipants): void
    {
        $signaturesPartLength = 128 + ($numberOfParticipants * 130);

        // Signatures is not deterministic so we need to remove them from the comparison
        $this->assertSame(substr($expected, 0, -$signaturesPartLength), substr($actual, 0, -$signaturesPartLength));
    }

    protected function assertSignaturesAreSerialized(string $serialized, array $signatures): void
    {
        foreach ($signatures as $signature) {
            $this->assertStringContainsString($signature, $serialized);
        }
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
}
