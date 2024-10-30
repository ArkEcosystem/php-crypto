<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Utils;

use kornrunner\Keccak;

abstract class AbiBase
{
    protected array $abi;

    public function __construct()
    {
        $abiFilePath = __DIR__.'/Abi.Consensus.json';

        $abiJson = file_get_contents($abiFilePath);

        $this->abi = json_decode($abiJson, true)['abi'];
    }

    protected function getArrayComponents(string $type): ?array
    {
        if (preg_match('/^(.*)\[(\d*)\]$/', $type, $matches)) {
            $innerType = $matches[1];
            $lengthStr = $matches[2];
            $length    = $lengthStr !== '' ? intval($lengthStr) : null;

            return [$length, $innerType];
        }

        return null;
    }

    protected function stripHexPrefix(string $hex): string
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
}
