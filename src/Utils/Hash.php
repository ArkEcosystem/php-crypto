<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Utils;

class Hash
{
    public static function sha256(string $data): string
    {
        return bin2hex(hash('sha256', $data, true));
    }
}
