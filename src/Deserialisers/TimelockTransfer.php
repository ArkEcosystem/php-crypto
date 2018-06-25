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

/**
 * This is the deserialiser class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class TimelockTransfer extends Deserialiser
{
    /**
     * Handle the deserialisation of "timelock transfer" data.
     *
     * @param int    $assetOffset
     * @param object $transaction
     *
     * @return object
     */
    public function handle(int $assetOffset, object $transaction): object
    {
        $transaction->amount       = UnsignedInteger::bit64($this->binary, $assetOffset / 2);
        $transaction->timelocktype = UnsignedInteger::bit8($this->binary, $assetOffset / 2 + 8) & 0xff;
        $transaction->timelock     = UnsignedInteger::bit32($this->binary, $assetOffset / 2 + 9);
        $transaction->recipientId  = Hex::high($this->binary, $assetOffset / 2 + 13, 42);
        $transaction->recipientId  = Base58::encodeCheck(new Buffer(hex2bin($transaction->recipientId)));

        return $this->parseSignatures($transaction, $assetOffset + (21 + 13) * 2);
    }
}
