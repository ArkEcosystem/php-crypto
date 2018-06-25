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

use BrianFaust\Binary\UnsignedInteger\Writer as UnsignedInteger;

/**
 * This is the serialiser class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class DelegateRegistration extends AbstractSerialiser
{
    /**
     * Handle the serialisation of "vote" data.
     *
     * @param string $bytes
     *
     * @return string
     */
    public function handle(string $bytes): string
    {
        $delegateBytes = bin2hex($this->transaction->asset->delegate->username);

        $bytes .= UnsignedInteger::bit8(strlen($delegateBytes) / 2);
        $bytes .= hex2bin($delegateBytes);

        return $bytes;
    }
}
