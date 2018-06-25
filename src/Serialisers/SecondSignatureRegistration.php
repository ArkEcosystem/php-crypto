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

/**
 * This is the serialiser class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class SecondSignatureRegistration extends Serialiser
{
    /**
     * Handle the serialisation of "delegate registration" data.
     *
     * @param string $bytes
     *
     * @return string
     */
    public function handle(string $bytes): string
    {
        $bytes .= hex2bin($this->transaction->asset['signature']['publicKey']);

        return $bytes;
    }
}
