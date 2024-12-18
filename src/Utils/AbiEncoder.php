<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Utils;

use Exception;

class AbiEncoder extends AbiBase
{
    public function encodeFunctionCall(string $functionName, array $args = []): string
    {
        $parameters = [
            'abi'          => $this->abi,
            'functionName' => $functionName,
            'args'         => $args,
        ];

        return $this->encodeFunctionData($parameters);
    }

    private function encodeFunctionData(array $parameters): string
    {
        $args = $parameters['args'] ?? [];

        list($abiItem, $functionName) = (function () use ($parameters) {
            if (
                count($parameters['abi']) === 1 &&
                isset($parameters['functionName']) &&
                substr($parameters['functionName'], 0, 2) === '0x'
            ) {
                return [$parameters['abi'][0], $parameters['functionName']];
            }

            return $this->prepareEncodeFunctionData($parameters);
        })();

        $signature = $functionName;

        if (! empty($abiItem['inputs'])) {
            $data = $this->encodeAbiParameters($abiItem['inputs'], $args);
        } else {
            $data = null;
        }

        return $this->concatHex([$signature, $data ?? '0x']);
    }

    private function prepareEncodeFunctionData(array $params): array
    {
        $abi          = $params['abi'];
        $functionName = $params['functionName'] ?? null;

        if (! $functionName) {
            $functions = array_filter($abi, function ($item) {
                return $item['type'] === 'function';
            });
            if (count($functions) === 1) {
                $abiItem      = array_values($functions)[0];
                $functionName = $abiItem['name'];
            } else {
                throw new Exception('Function name is not provided and ABI has multiple functions');
            }
        }

        $abiItem = $this->getAbiItem($abi, $functionName, $params['args'] ?? []);
        if (! $abiItem) {
            throw new Exception('Function not found in ABI: '.$functionName);
        }

        $signature = $this->toFunctionSelector($abiItem);

        return [$abiItem, $signature];
    }

    private function getAbiItem(array $abi, string $name, array $args): array
    {
        $matchingItems = array_filter($abi, function ($item) use ($name) {
            return $item['type'] === 'function' && $item['name'] === $name;
        });

        if (count($matchingItems) === 0) {
            throw new Exception("Function not found in ABI: $name");
        }

        foreach ($matchingItems as $item) {
            $inputs = $item['inputs'];
            if (count($inputs) === count($args)) {
                return $item;
            }
        }

        throw new Exception("Function with matching arguments not found in ABI: $name");
    }

    private function encodeAbiParameters(array $params, array $values): string
    {
        if (count($params) !== count($values)) {
            throw new Exception('Length of parameters and values do not match');
        }

        $preparedParams = $this->prepareParams($params, $values);
        $data           = $this->encodeParams($preparedParams);

        return $data !== '' ? $data : '0x';
    }

    private function prepareParams(array $params, array $values): array
    {
        $preparedParams = [];
        foreach ($params as $index => $param) {
            $preparedParam    = $this->prepareParam($param, $values[$index]);
            $preparedParams[] = $preparedParam;
        }

        return $preparedParams;
    }

    private function prepareParam(array $param, $value): array
    {
        $arrayComponents = $this->getArrayComponents($param['type']);
        if ($arrayComponents) {
            list($length, $type) = $arrayComponents;

            return $this->encodeArray($value, $length, ['name' => $param['name'], 'type' => $type]);
        }
        if ($param['type'] === 'tuple') {
            return $this->encodeTuple($value, $param);
        }
        if ($param['type'] === 'address') {
            return $this->encodeAddress($value);
        }
        if ($param['type'] === 'bool') {
            return $this->encodeBool($value);
        }
        if (str_starts_with($param['type'], 'uint') || str_starts_with($param['type'], 'int')) {
            $signed = str_starts_with($param['type'], 'int');

            return $this->encodeNumber($value, $signed);
        }
        if (str_starts_with($param['type'], 'bytes')) {
            return $this->encodeBytes($value, $param);
        }
        if ($param['type'] === 'string') {
            return $this->encodeString($value);
        }

        throw new Exception('Invalid ABI type: '.$param['type']);
    }

    private function encodeArray($value, ?int $length, array $param): array
    {
        $dynamic = $length === null;

        if (! is_array($value)) {
            throw new Exception('Invalid array value');
        }
        if (! $dynamic && count($value) !== $length) {
            throw new Exception('Array length mismatch');
        }

        $dynamicChild   = false;
        $preparedParams = [];
        foreach ($value as $v) {
            $preparedParam = $this->prepareParam($param, $v);
            if ($preparedParam['dynamic']) {
                $dynamicChild = true;
            }
            $preparedParams[] = $preparedParam;
        }

        if ($dynamic || $dynamicChild) {
            $data = $this->encodeParams($preparedParams);
            if ($dynamic) {
                $lengthHex = str_pad(dechex(count($preparedParams)), 64, '0', STR_PAD_LEFT);

                return [
                    'dynamic' => true,
                    'encoded' => '0x'.$lengthHex.substr($data, 2),
                ];
            }
            if ($dynamicChild) {
                return [
                    'dynamic' => true,
                    'encoded' => $data,
                ];
            }
        }
        $encoded = '';
        foreach ($preparedParams as $p) {
            $encoded .= substr($p['encoded'], 2);
        }

        return [
            'dynamic' => false,
            'encoded' => '0x'.$encoded,
        ];
    }

    private function encodeParams(array $preparedParams): string
    {
        $staticSize = 0;
        foreach ($preparedParams as $param) {
            $staticSize += $param['dynamic'] ? 32 : (strlen($param['encoded']) - 2) / 2;
        }

        $staticParams  = [];
        $dynamicParams = [];
        $dynamicSize   = 0;
        foreach ($preparedParams as $param) {
            if ($param['dynamic']) {
                $offset          = str_pad(dechex($staticSize + $dynamicSize), 64, '0', STR_PAD_LEFT);
                $staticParams[]  = $offset;
                $dynamicParams[] = substr($param['encoded'], 2);
                $dynamicSize += (strlen($param['encoded']) - 2) / 2;
            } else {
                $staticParams[] = substr($param['encoded'], 2);
            }
        }

        $encoded = '0x'.implode('', $staticParams).implode('', $dynamicParams);

        return $encoded;
    }

    private function encodeAddress(string $value): array
    {
        if (! $this->isValidAddress($value)) {
            throw new Exception('Invalid address: '.$value);
        }
        $value = strtolower(substr($value, 2));

        return [
            'dynamic' => false,
            'encoded' => '0x'.str_pad($value, 64, '0', STR_PAD_LEFT),
        ];
    }

    private function encodeBool(bool $value): array
    {
        $encoded = str_pad($value ? '1' : '0', 64, '0', STR_PAD_LEFT);

        return [
            'dynamic' => false,
            'encoded' => '0x'.$encoded,
        ];
    }

    private function encodeNumber($value, bool $signed): array
    {
        if (! is_numeric($value)) {
            throw new Exception('Invalid number value');
        }
        if ($signed) {
            $gmpValue = gmp_init($value, 10);
            if (gmp_cmp($gmpValue, 0) < 0) {
                $gmpValue = gmp_add(gmp_pow(2, 256), $gmpValue);
            }
        } else {
            if ($value < 0) {
                throw new Exception('Negative value provided for unsigned integer type');
            }
            $gmpValue = gmp_init($value, 10);
        }
        $hex     = gmp_strval($gmpValue, 16);
        $encoded = str_pad($hex, 64, '0', STR_PAD_LEFT);

        return [
            'dynamic' => false,
            'encoded' => '0x'.$encoded,
        ];
    }

    private function encodeBytes(string $value, array $param): array
    {
        $bytesSize    = (strlen($value) - 2) / 2;
        $paramSizeStr = substr($param['type'], 5);
        if ($paramSizeStr === '') {
            $lengthHex   = str_pad(dechex($bytesSize), 64, '0', STR_PAD_LEFT);
            $valuePadded = $value;
            $padding     = (32 - ($bytesSize % 32)) % 32;
            if ($padding > 0) {
                $valuePadded .= str_repeat('0', $padding * 2);
            }

            return [
                'dynamic' => true,
                'encoded' => '0x'.$lengthHex.substr($valuePadded, 2),
            ];
        }
        $paramSize = intval($paramSizeStr);
        if ($bytesSize !== $paramSize) {
            throw new Exception("Bytes size mismatch: expected $paramSize, got $bytesSize");
        }
        $valuePadded = str_pad(substr($value, 2), 64, '0', STR_PAD_RIGHT);

        return [
            'dynamic' => false,
            'encoded' => '0x'.$valuePadded,
        ];
    }

    private function encodeString(string $value): array
    {
        $hexValue    = bin2hex($value);
        $lengthHex   = str_pad(dechex(strlen($value)), 64, '0', STR_PAD_LEFT);
        $valuePadded = $hexValue;
        $padding     = (32 - (strlen($value) % 32)) % 32;
        if ($padding > 0) {
            $valuePadded .= str_repeat('00', $padding);
        }

        return [
            'dynamic' => true,
            'encoded' => '0x'.$lengthHex.$valuePadded,
        ];
    }

    private function encodeTuple($value, array $param): array
    {
        $dynamic        = false;
        $preparedParams = [];
        foreach ($param['components'] as $index => $component) {
            $key = is_array($value) ? $index : $component['name'];
            if (! isset($value[$key])) {
                throw new Exception('Tuple value missing component: '.$component['name']);
            }
            $preparedParam = $this->prepareParam($component, $value[$key]);
            if ($preparedParam['dynamic']) {
                $dynamic = true;
            }
            $preparedParams[] = $preparedParam;
        }
        if ($dynamic) {
            $encoded = $this->encodeParams($preparedParams);

            return [
                'dynamic' => true,
                'encoded' => $encoded,
            ];
        }
        $encoded = '0x'.implode('', array_map(fn ($p) => substr($p['encoded'], 2), $preparedParams));

        return [
            'dynamic' => false,
            'encoded' => $encoded,
        ];
    }

    private function concatHex(array $hexes): string
    {
        $result = '0x';
        foreach ($hexes as $hex) {
            if (empty($hex) || $hex === '0x') {
                continue;
            }
            $result .= $this->stripHexPrefix($hex);
        }

        return $result;
    }
}
