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

namespace ArkEcosystem\Crypto\Deserialisers;

use BrianFaust\Binary\UnsignedInteger\Reader as UnsignedInteger;
use stdClass;

/**
 * This is the deserialiser class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class DelegateRegistration extends Deserialiser
{
    /**
     * Handle the deserialisation of "vote" data.
     *
     * @param int    $assetOffset
     * @param object $transaction
     *
     * @return object
     */
    public function handle(int $assetOffset, object $transaction): object
    {
        $usernameLength = UnsignedInteger::bit8($this->binary, $assetOffset / 2) & 0xff;

        $transaction->asset                     = new stdClass();
        $transaction->asset->delegate           = new stdClass();
        $transaction->asset->delegate->username = hex2bin(substr($this->hex, $assetOffset + 2, $usernameLength * 2));

        return $this->parseSignatures($transaction, $assetOffset + ($usernameLength + 1) * 2);
    }
}
