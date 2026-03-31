<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Utils;

use ArkEcosystem\Crypto\Enums\ContractAbiType;
use kornrunner\Keccak;

abstract class AbiBase
{
    protected array $abi;

    protected array $functionSelectorMap = [];

    protected array $errorSelectorMap = [];

    public function __construct(ContractAbiType $type = ContractAbiType::CONSENSUS, ?string $path = null)
    {
        $abiFilePath = self::contractAbiPath($type, $path);
        $decodedAbi  = self::loadAbiJson($abiFilePath);

        $this->abi = $decodedAbi['abi'];

        foreach ($this->abi as $item) {
            $signature = $this->getFunctionSignature($item);
            $selector  = substr($this->keccak256($signature), 2, 8);

            if ($item['type'] === 'function') {
                $this->functionSelectorMap[$selector] = $item;
            } elseif ($item['type'] === 'error') {
                $this->errorSelectorMap[$selector] = $item;
            }
        }
    }

    public static function methodIdentifiers(
        ContractAbiType $type = ContractAbiType::CONSENSUS,
        ?string $path = null
    ): array {
        $abiFilePath = self::contractAbiPath($type, $path);
        $decodedAbi  = self::loadAbiJson($abiFilePath);

        if (! isset($decodedAbi['methodIdentifiers']) || ! is_array($decodedAbi['methodIdentifiers'])) {
            throw new \RuntimeException("ABI JSON does not contain methodIdentifiers: {$abiFilePath}");
        }

        return $decodedAbi['methodIdentifiers'];
    }

    protected static function getArrayComponents(string $type): ?array
    {
        if (preg_match('/^(.*)\[(\d*)\]$/', $type, $matches)) {
            $innerType = $matches[1];
            $lengthStr = $matches[2];
            $length    = $lengthStr !== '' ? intval($lengthStr) : null;

            return [$length, $innerType];
        }

        return null;
    }

    protected static function stripHexPrefix(string $hex): string
    {
        if (substr($hex, 0, 2) === '0x') {
            return substr($hex, 2);
        }

        return $hex;
    }

    protected function isValidAddress($address): bool
    {
        return is_string($address)
            && str_starts_with($address, '0x')
            && strlen($address) === 42
            && ctype_xdigit(substr($address, 2));
    }

    protected function keccak256(string $input): string
    {
        return '0x'.Keccak::hash($input, 256);
    }

    protected function getFunctionSignature(array $abiItem): string
    {
        $name   = $abiItem['name'];
        $inputs = $abiItem['inputs'];
        $types  = array_map(function ($input) {
            return $input['type'];
        }, $inputs);

        return $name.'('.implode(',', $types).')';
    }

    protected function toFunctionSelector(array $abiItem): string
    {
        $signature = $this->getFunctionSignature($abiItem);
        $hash      = $this->keccak256($signature);
        $selector  = '0x'.substr($hash, 2, 8);

        return $selector;
    }

    protected static function contractAbiPath(ContractAbiType $type, ?string $path = null): string
    {
        return match ($type) {
            ContractAbiType::CONSENSUS           => __DIR__.'/Abi/json/Abi.Consensus.json',
            ContractAbiType::MULTIPAYMENT        => __DIR__.'/Abi/json/Abi.Multipayment.json',
            ContractAbiType::USERNAMES           => __DIR__.'/Abi/json/Abi.Usernames.json',
            ContractAbiType::ERC20BATCH_TRANSFER => __DIR__.'/Abi/json/Abi.ERC20BatchTransfer.json',
            ContractAbiType::TOKEN               => __DIR__.'/Abi/json/Abi.Token.json',
            ContractAbiType::CUSTOM              => (function () use ($path): string {
                if ($path === null || $path === '') {
                    throw new \InvalidArgumentException('A non-empty $path must be provided when using ContractAbiType::CUSTOM.');
                }

                return $path;
            })(),
        };
    }

    private static function loadAbiJson(string $path): array
    {
        $rawJson = file_get_contents($path);

        if ($rawJson === false) {
            throw new \RuntimeException("Unable to load ABI JSON: {$path}");
        }

        $decoded = json_decode($rawJson, true);

        if (! is_array($decoded) || ! isset($decoded['abi']) || ! is_array($decoded['abi'])) {
            throw new \RuntimeException("ABI JSON does not contain a valid abi array: {$path}");
        }

        return $decoded;
    }
}
