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

namespace ArkEcosystem\Crypto\Deserializers;

use BitWasp\Bitcoin\Base58;
use BitWasp\Buffertools\Buffer;
use BrianFaust\Binary\Hex\Reader as Hex;
use BrianFaust\Binary\UnsignedInteger\Reader as UnsignedInteger;

/**
 * This is the deserializer class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class TimelockTransfer extends AbstractDeserializer
{
    /**
     * Handle the deserialisation of "timelock transfer" data.
     *
     * @return object
     */
    public function deserialize(): object
    {
        $this->transaction->amount       = UnsignedInteger::bit64($this->binary, $this->assetOffset / 2);
        $this->transaction->timelocktype = UnsignedInteger::bit8($this->binary, $this->assetOffset / 2 + 8) & 0xff;
        $this->transaction->timelock     = UnsignedInteger::bit32($this->binary, $this->assetOffset / 2 + 9);
        $this->transaction->recipientId  = Hex::high($this->binary, $this->assetOffset / 2 + 13, 42);
        $this->transaction->recipientId  = Base58::encodeCheck(new Buffer(hex2bin($this->transaction->recipientId)));

        return $this->parseSignatures($this->assetOffset + (21 + 13) * 2);
    }
}
