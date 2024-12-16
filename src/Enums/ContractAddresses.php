<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Enums;

enum ContractAbiType: string
{
    case CONSENSUS    = '0x535B3D7A252fa034Ed71F0C53ec0C6F784cB64E1';
    case MULTIPAYMENT = '0x83769BeEB7e5405ef0B7dc3C66C43E3a51A6d27f';
    case USERNAMES    = '0x2c1DE3b4Dbb4aDebEbB5dcECAe825bE2a9fc6eb6';
}
