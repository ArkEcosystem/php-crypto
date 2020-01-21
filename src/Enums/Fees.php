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
    const TRANSFER = '10000000';

    const SECOND_SIGNATURE_REGISTRATION = '500000000';

    const DELEGATE_REGISTRATION = '2500000000';

    const VOTE = '100000000';

    const MULTI_SIGNATURE_REGISTRATION = '500000000';

    const IPFS = '500000000';

    const MULTI_PAYMENT = '10000000';

    const DELEGATE_RESIGNATION = '2500000000';

    const HTLC_LOCK = '10000000';

    const HTLC_CLAIM = '0';

    const HTLC_REFUND = '0';
}
