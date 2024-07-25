<?php

declare(strict_types=1);

/*
 * This file is part of Ark PHP Crypto.
 *
 * (c) Ark Ecosystem <info@ark.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ArkEcosystem\Crypto\Enums;

/**
 * This is the transaction fees class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class Fees
{
    public const TRANSFER = '10000000';

    public const SECOND_SIGNATURE_REGISTRATION = '500000000';

    public const VALIDATOR_REGISTRATION = '2500000000';

    public const VOTE = '100000000';

    public const MULTI_SIGNATURE_REGISTRATION = '500000000';

    public const IPFS = '500000000';

    public const MULTI_PAYMENT = '10000000';

    public const VALIDATOR_RESIGNATION = '2500000000';

    public const USERNAME_REGISTRATION = '2500000000';

    public const USERNAME_RESIGNATION = '2500000000';

    public const HTLC_REFUND = '0';
}
