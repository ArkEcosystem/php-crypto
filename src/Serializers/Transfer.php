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

namespace ArkEcosystem\Crypto\Serializers;

use BitWasp\Bitcoin\Base58;
use BrianFaust\Binary\Hex\Writer as Hex;
use BrianFaust\Binary\UnsignedInteger\Writer as UnsignedInteger;

/**
 * This is the serializer class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class Transfer extends AbstractSerializer
{
    /**
     * Handle the serialisation of "transfer" data.
     *
     * @return string
     */
    public function serialize(): string
    {
        $this->bytes .= UnsignedInteger::bit64($this->transaction->amount);
        $this->bytes .= UnsignedInteger::bit32($this->transaction->expiration ?? 0);

        $recipientId = Base58::decodeCheck($this->transaction->recipientId)->getHex();

        $this->bytes .= Hex::high($recipientId, strlen($recipientId));

        return $this->bytes;
    }
}
