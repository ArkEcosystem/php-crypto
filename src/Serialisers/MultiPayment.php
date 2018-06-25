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

namespace ArkEcosystem\Crypto\Serialisers;

use BitWasp\Bitcoin\Base58;
use BrianFaust\Binary\Hex\Writer as Hex;
use BrianFaust\Binary\UnsignedInteger\Writer as UnsignedInteger;

/**
 * This is the serialiser class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class MultiPayment extends AbstractSerialiser
{
    /**
     * Handle the serialisation of "multi payment" data.
     *
     * @param string $bytes
     *
     * @return string
     */
    public function handle(string $bytes): string
    {
        $bytes .= UnsignedInteger::bit32(count($this->transaction->asset['payments']));

        foreach ($this->transaction->asset['payments'] as $payment) {
            $bytes .= UnsignedInteger::bit64($payment->amount);

            $recipientId = Base58::decodeCheck($payment->recipientId)->getHex();
            $bytes .= Hex::high($recipientId, strlen($recipientId));
        }

        return $bytes;
    }
}
