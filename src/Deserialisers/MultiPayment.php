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

use BitWasp\Bitcoin\Base58;
use BitWasp\Buffertools\Buffer;
use BrianFaust\Binary\Hex\Reader as Hex;
use BrianFaust\Binary\UnsignedInteger\Reader as UnsignedInteger;
use stdClass;

/**
 * This is the deserialiser class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class MultiPayment extends AbstractDeserialiser
{
    /**
     * Handle the deserialisation of "multi payment" data.
     *
     * @return object
     */
    public function deserialise(): object
    {
        $this->transaction->asset = new stdClass();

        [
            'payments' => [],
        ];

        $total  = UnsignedInteger::bit8($this->binary, $this->assetOffset / 2)[1] & 0xff;
        $offset = $this->assetOffset / 2 + 1;

        for ($i = 0; $i < $total; ++$i) {
            $payment              = new stdClass();
            $payment->amount      = UnsignedInteger::bit64($this->binary, $offset);
            $payment->recipientId = Hex::high($this->binary, $offset + 1, 42);
            $payment->recipientId = Base58::encodeCheck(new Buffer(hex2bin($payment['recipientId'])));

            $this->transaction->asset['payments'][] = $payment;

            $offset += 22;
        }

        $this->transaction->amount = array_sum(array_column($this->transaction->asset['payments'], 'amount'));

        return $this->parseSignatures($offset * 2);
    }
}
