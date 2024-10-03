<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Enums;

class Fees
{
    public const TRANSFER = '10000000';

    public const VALIDATOR_REGISTRATION = '2500000000';

    public const VOTE = '100000000';

    public const MULTI_SIGNATURE_REGISTRATION = '500000000';

    public const MULTI_PAYMENT = '10000000';

    public const VALIDATOR_RESIGNATION = '2500000000';

    public const USERNAME_REGISTRATION = '2500000000';

    public const USERNAME_RESIGNATION = '2500000000';

    // @TODO: review this fee
    public const EVM = '0';
}
