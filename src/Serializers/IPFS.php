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

use BrianFaust\Binary\UnsignedInteger\Writer as UnsignedInteger;

/**
 * This is the serializer class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class IPFS extends AbstractSerializer
{
    /**
     * Handle the serialisation of "ipfs" data.
     *
     * @return string
     */
    public function serialize(): string
    {
        $dag = $this->transaction->asset->ipfs->dag;

        $this->bytes .= UnsignedInteger::bit8(strlen($dag) / 2);
        $this->bytes .= hex2bin($dag);

        return $this->bytes;
    }
}
