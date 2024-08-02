<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Enums;

class TypeGroup
{
    public const TEST = 0;

    public const CORE = 1;

    public const RESERVED = 1000; // Everything above is available to anyone
}
