<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Utils;

use Exception;

class AbiDecoder extends AbiBase
{
    public function decodeFunctionData(string $data): array
    {
        $data = self::stripHexPrefix($data);

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

    public function decodeError(string $data): string
    {
        $data = $this->stripHexPrefix($data);

        $errorSelector = substr($data, 0, 8);

        $abiItem = $this->findErrorBySelector($errorSelector);
        if (! $abiItem) {
            throw new Exception('Function selector not found in ABI: '.$errorSelector);
        }

        return $abiItem['name'];
    }

    public static function decodeAddress(string $bytes, int $offset): array
    {
        $data         = substr($bytes, $offset, 32);
        $addressBytes = substr($data, 12, 20);
        $address      = Address::toChecksumAddress('0x'.bin2hex($addressBytes));

        return [$address, 32];
    }

    public static function decodeBool(string $bytes, int $offset): array
    {
        $data  = substr($bytes, $offset, 32);
        $value = hexdec(bin2hex($data)) !== 0;

        return [$value, 32];
    }

    public static function decodeNumber(string $bytes, int $offset, int $bits, bool $signed): array
    {
        $data  = substr($bytes, $offset, 32);
        $hex   = bin2hex($data);
        $value = gmp_import(hex2bin($hex), 1, GMP_MSW_FIRST | GMP_BIG_ENDIAN);
        if ($signed && gmp_testbit($value, $bits - 1)) {
            $value = gmp_sub($value, gmp_pow(2, $bits));
        }

        return [gmp_strval($value), 32];
    }

    public static function decodeString(string $bytes, int $offset): array
    {
        $dataOffset   = self::readUInt($bytes, $offset);
        $stringOffset = $dataOffset;
        $length       = self::readUInt($bytes, $stringOffset);
        $stringData   = substr($bytes, $stringOffset + 32, $length);
        $value        = $stringData;

        return [$value, 32];
    }

    public static function decodeDynamicBytes(string $bytes, int $offset): array
    {
        $dataOffset  = self::readUInt($bytes, $offset);
        $bytesOffset = $dataOffset;
        $length      = self::readUInt($bytes, $bytesOffset);
        $bytesData   = substr($bytes, $bytesOffset + 32, $length);
        $value       = '0x'.bin2hex($bytesData);

        return [$value, 32];
    }

    public static function decodeFixedBytes(string $bytes, int $offset, int $size): array
    {
        $data  = substr($bytes, $offset, 32);
        $value = '0x'.substr(bin2hex($data), 0, $size * 2);

        return [$value, 32];
    }

    public static function decodeArray(string $bytes, int $offset, array $param, ?int $length): array
    {
        $baseType            = $param['type'];
        $elementType         = $param;
        $elementType['type'] = $baseType;

        if ($length === null) {
            // Read the offset to the dynamic data
            $dataOffset = self::readUInt($bytes, $offset);

            // Read the array length
            $arrayLength = self::readUInt($bytes, $dataOffset);

            $cursor = $dataOffset + 32;
        } else {
            $arrayLength = $length;
            $cursor      = $offset;
        }

        $values = [];
        for ($i = 0; $i < $arrayLength; $i++) {
            list($value, $consumed) = self::decodeParameter($bytes, $cursor, $elementType);
            $cursor += $consumed;
            $values[] = $value;
        }

        return [$values, 32];
    }

    public static function decodeTuple(string $bytes, int $offset, array $param): array
    {
        $components = $param['components'];
        $values     = [];
        $cursor     = $offset;

        foreach ($components as $component) {
            list($value, $consumed) = self::decodeParameter($bytes, $cursor, $component);
            $cursor += $consumed;
            $values[$component['name'] ?? ''] = $value;
        }

        return [$values, 32];
    }

    public static function readUInt(string $bytes, int $offset): int
    {
        $data = substr($bytes, $offset, 32);

        return hexdec(bin2hex($data));
    }

    /**
     * Decodes the output of a function call using a compact function signature
     * like "function name() view returns (string)" and the hex payload from eth_call.
     */
    public static function decodeFunctionWithAbi(string $functionSignature, string $payload): array
    {
        $abiItem = self::parseFunctionSignature($functionSignature);

        return self::decodeFunctionOutput($abiItem, $payload);
    }

    private function findFunctionBySelector(string $selector): ?array
    {
        return $this->functionSelectorMap[$selector] ?? null;
    }

    private function findErrorBySelector(string $selector): ?array
    {
        return $this->errorSelectorMap[$selector] ?? null;
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
            list($value, $consumed) = self::decodeParameter($bytes, $cursor, $param);
            $cursor += $consumed;
            $values[] = $value;
        }

        return $values;
    }

    private static function decodeParameter(string $bytes, int $offset, array $param): array
    {
        $type            = $param['type'];
        $arrayComponents = self::getArrayComponents($type);
        if ($arrayComponents) {
            list($length, $baseType) = $arrayComponents;
            $param['type']           = $baseType;

            return self::decodeArray($bytes, $offset, $param, $length);
        }

        switch ($type) {
            case 'address':
                return self::decodeAddress($bytes, $offset);
            case 'bool':
                return self::decodeBool($bytes, $offset);
            case 'string':
                return self::decodeString($bytes, $offset);
            case 'bytes':
                return self::decodeDynamicBytes($bytes, $offset);
            default:
                if (preg_match('/^bytes(\d+)$/', $type, $matches)) {
                    $size = intval($matches[1]);

                    return self::decodeFixedBytes($bytes, $offset, $size);
                } elseif (preg_match('/^(u?int)(\d+)$/', $type, $matches)) {
                    $signed = $matches[1] === 'int';
                    $bits   = intval($matches[2]);

                    return self::decodeNumber($bytes, $offset, $bits, $signed);
                } elseif ($type === 'tuple') {
                    return self::decodeTuple($bytes, $offset, $param);
                }

                throw new Exception('Unsupported type: '.$type);
        }
    }

    private static function parseFunctionSignature(string $signature): array
    {
        $pattern = '/function\s+(\w+)\s*\(([^)]*)\)\s*(?:\w*\s*)*returns\s*\(([^)]*)\)/';
        if (! preg_match($pattern, $signature, $matches)) {
            throw new \InvalidArgumentException("Invalid function signature: $signature");
        }

        $functionName = $matches[1];
        $rawInputs    = trim($matches[2]);
        $rawOutputs   = trim($matches[3]);

        $inputs  = [];
        $outputs = [];

        if ($rawInputs !== '') {
            foreach (explode(',', $rawInputs) as $inputPart) {
                $inputs[] = ['type' => trim($inputPart)];
            }
        }

        if ($rawOutputs !== '') {
            foreach (explode(',', $rawOutputs) as $outputPart) {
                $outputs[] = ['type' => trim($outputPart)];
            }
        }

        return [
            'type'    => 'function',
            'name'    => $functionName,
            'inputs'  => $inputs,
            'outputs' => $outputs,
        ];
    }

    private static function decodeFunctionOutput(array $abiItem, string $payload): array
    {
        $hex   = self::stripHexPrefix($payload);
        $bytes = hex2bin($hex);

        $cursor  = 0;
        $outputs = $abiItem['outputs'] ?? [];
        $decoded = [];

        foreach ($outputs as $param) {
            list($value, $consumed) = self::decodeParameter($bytes, $cursor, $param);
            $cursor += $consumed;
            $decoded[] = $value;
        }

        return $decoded;
    }
}
