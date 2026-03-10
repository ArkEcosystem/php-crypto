<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Enums;

enum ContractAbiType: string

    case CUSTOM              = 'custom';
    case CONSENSUS           = 'consensus';
    case ERC20BATCH_TRANSFER = 'erc20batchtransfer';
    case MULTIPAYMENT        = 'multipayment';
    case USERNAMES           = 'usernames';
    case TOKEN               = 'token';
}
