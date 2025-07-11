<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Utils\Abi;

use ArkEcosystem\Crypto\Utils\AbiDecoder;

final class ArgumentDecoder
{
    private string $bytes;

    public function __construct(string $bytes)
    {
        try {
            $bytes = hex2bin($bytes);
        } catch (\Throwable $e) {
            // Handle the case where hex2bin fails, e.g., invalid hex string
            $bytes = false;
        }

        if ($bytes === false) {
            $bytes = '';
        }

        $this->bytes = $bytes;
    }

    public function decodeString(): string
    {
        [$value] = AbiDecoder::decodeString($this->bytes, 0);

        return $value;
    }

    public function decodeAddress(): string
    {
        [$value] = AbiDecoder::decodeAddress($this->bytes, 0);

        return $value;
    }

    public function decodeUnsignedInt(): string
    {
        [$value] = AbiDecoder::decodeNumber($this->bytes, 0, 32, false);

        return $value;
    }

    public function decodeSignedInt(): string
    {
        [$value] = AbiDecoder::decodeNumber($this->bytes, 0, 32, true);

        return $value;
    }

    public function decodeBool(): bool
    {
        [$value] = AbiDecoder::decodeBool($this->bytes, 0);

        return $value;
    }
}
