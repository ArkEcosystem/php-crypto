<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Utils;

use Exception;

class AbiDecoder extends AbiBase
{
    public function decodeFunctionData(string $data): array
    {
        $data = $this->stripHexPrefix($data);

        $functionSelector = substr($data, 0, 8);

        $abiItem = $this->findFunctionBySelector($functionSelector);
        if (! $abiItem) {
            throw new Exception('Function selector not found in ABI: '.$functionSelector);
        }

        $encodedParams = substr($data, 8);
        $decodedParams = $this->decodeAbiParameters($abiItem['inputs'], $encodedParams);

        return [
            'functionName' => $abiItem['name'],
            'args'         => $decodedParams,
        ];
    }

    private function findFunctionBySelector(string $selector): ?array
    {
        foreach ($this->abi as $item) {
            if ($item['type'] === 'function') {
                $functionSignature = $this->getFunctionSignature($item);
                $functionSelector  = substr($this->keccak256($functionSignature), 2, 8);
                if ($functionSelector === $selector) {
                    return $item;
                }
            }
        }

        return null;
    }

    private function decodeAbiParameters(array $params, string $data): array
    {
        if (empty($data) && count($params) > 0) {
            throw new Exception('No data to decode');
        }

        $bytes  = hex2bin($data);
        $cursor = 0;

        $values = [];
        foreach ($params as $param) {
            list($value, $consumed) = $this->decodeParameter($bytes, $cursor, $param);
            $cursor += $consumed;
            $values[] = $value;
        }

        return $values;
    }

    private function decodeParameter(string $bytes, int $offset, array $param): array
    {
        $type            = $param['type'];
        $arrayComponents = $this->getArrayComponents($type);
        if ($arrayComponents) {
            list($length, $baseType) = $arrayComponents;
            $param['type']           = $baseType;

            return $this->decodeArray($bytes, $offset, $param, $length);
        }

        switch ($type) {
            case 'address':
                return $this->decodeAddress($bytes, $offset);
            case 'bool':
                return $this->decodeBool($bytes, $offset);
            case 'string':
                return $this->decodeString($bytes, $offset);
            case 'bytes':
                return $this->decodeDynamicBytes($bytes, $offset);
            default:
                if (preg_match('/^bytes(\d+)$/', $type, $matches)) {
                    $size = intval($matches[1]);

                    return $this->decodeFixedBytes($bytes, $offset, $size);
                } elseif (preg_match('/^(u?int)(\d+)$/', $type, $matches)) {
                    $signed = $matches[1] === 'int';
                    $bits   = intval($matches[2]);

                    return $this->decodeNumber($bytes, $offset, $bits, $signed);
                } elseif ($type === 'tuple') {
                    return $this->decodeTuple($bytes, $offset, $param);
                }

                throw new Exception('Unsupported type: '.$type);
        }
    }

    private function decodeAddress(string $bytes, int $offset): array
    {
        $data         = substr($bytes, $offset, 32);
        $addressBytes = substr($data, 12, 20);
        $address      = Address::toChecksumAddress('0x'.bin2hex($addressBytes));

        return [$address, 32];
    }

    private function decodeBool(string $bytes, int $offset): array
    {
        $data  = substr($bytes, $offset, 32);
        $value = hexdec(bin2hex($data)) !== 0;

        return [$value, 32];
    }

    private function decodeNumber(string $bytes, int $offset, int $bits, bool $signed): array
    {
        $data  = substr($bytes, $offset, 32);
        $hex   = bin2hex($data);
        $value = gmp_import(hex2bin($hex), 1, GMP_MSW_FIRST | GMP_BIG_ENDIAN);
        if ($signed && gmp_testbit($value, $bits - 1)) {
            $value = gmp_sub($value, gmp_pow(2, $bits));
        }

        return [gmp_strval($value), 32];
    }

    private function decodeString(string $bytes, int $offset): array
    {
        $dataOffset   = $this->readUInt($bytes, $offset);
        $stringOffset = $offset + $dataOffset;
        $length       = $this->readUInt($bytes, $stringOffset);
        $stringData   = substr($bytes, $stringOffset + 32, $length);
        $value        = $stringData;

        return [$value, 32];
    }

    private function decodeDynamicBytes(string $bytes, int $offset): array
    {
        $dataOffset  = $this->readUInt($bytes, $offset);
        $bytesOffset = $offset + $dataOffset;
        $length      = $this->readUInt($bytes, $bytesOffset);
        $bytesData   = substr($bytes, $bytesOffset + 32, $length);
        $value       = '0x'.bin2hex($bytesData);

        return [$value, 32];
    }

    private function decodeFixedBytes(string $bytes, int $offset, int $size): array
    {
        $data  = substr($bytes, $offset, 32);
        $value = '0x'.substr(bin2hex($data), 0, $size * 2);

        return [$value, 32];
    }

    private function decodeArray(string $bytes, int $offset, array $param, ?int $length): array
    {
        $baseType            = $param['type'];
        $elementType         = $param;
        $elementType['type'] = $baseType;

        if ($length === null) {
            $dataOffset  = $this->readUInt($bytes, $offset);
            $arrayOffset = $offset + $dataOffset;
            $arrayLength = $this->readUInt($bytes, $arrayOffset);
            $cursor      = $arrayOffset + 32;
        } else {
            $arrayLength = $length;
            $cursor      = $offset;
        }

        $values = [];
        for ($i = 0; $i < $arrayLength; $i++) {
            list($value, $consumed) = $this->decodeParameter($bytes, $cursor, $elementType);
            $cursor += $consumed;
            $values[] = $value;
        }

        return [$values, 32];
    }

    private function decodeTuple(string $bytes, int $offset, array $param): array
    {
        $components = $param['components'];
        $values     = [];
        $cursor     = $offset;

        foreach ($components as $component) {
            list($value, $consumed) = $this->decodeParameter($bytes, $cursor, $component);
            $cursor += $consumed;
            $values[$component['name'] ?? ''] = $value;
        }

        return [$values, 32];
    }

    private function readUInt(string $bytes, int $offset): int
    {
        $data = substr($bytes, $offset, 32);

        return hexdec(bin2hex($data));
    }
}
