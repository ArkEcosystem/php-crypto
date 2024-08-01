<?php

declare(strict_types=1);



namespace ArkEcosystem\Crypto\Enums;

/**
 * This is the transaction fees class.
 *
 * @author Brian Faust <brian@ark.io>
 */
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
}
