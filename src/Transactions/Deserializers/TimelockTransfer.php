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

namespace ArkEcosystem\Crypto\Transactions\Deserializers;

use BitWasp\Bitcoin\Base58;
use BitWasp\Buffertools\Buffer;

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
        $this->buffer->position($this->assetOffset / 2);

        $this->transaction->amount       = $this->buffer->readUInt64();
        $this->transaction->timelockType = $this->buffer->readUInt8() & 0xff;
        $this->transaction->timelock     = $this->buffer->readUInt32();
        $this->transaction->recipientId  = Base58::encodeCheck(new Buffer(hex2bin($this->buffer->readHex(21))));

        return $this->parseSignatures($this->assetOffset + (8 + 1 + 4 + 21) * 2);
    }
}
