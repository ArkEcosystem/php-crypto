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
 * This is the transaction types class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class Types
{
    public const TRANSFER = 0;

    public const SECOND_SIGNATURE_REGISTRATION = 1;

    public const DELEGATE_REGISTRATION = 2;

    public const VOTE = 3;

    public const MULTI_SIGNATURE_REGISTRATION = 4;

    public const IPFS = 5;

    public const MULTI_PAYMENT = 6;

    public const DELEGATE_RESIGNATION = 7;

    public const HTLC_LOCK = 8;

    public const HTLC_CLAIM = 9;

    public const HTLC_REFUND = 10;
}
