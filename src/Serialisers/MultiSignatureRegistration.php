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
class MultiSignatureRegistration extends Serialiser
{
    /**
     * Handle the serialisation of "multi signature registration" data.
     *
     * @param string $bytes
     *
     * @return string
     */
    public function handle(string $bytes): string
    {
        $keysgroup = [];

        if (!isset($transaction->version) || 1 === $transaction->version) {
            foreach ($this->transaction->asset['multisignature']['keysgroup'] as $key) {
                $keysgroup[] = substr($key, 1);
            }
        } else {
            $keysgroup = $this->transaction->asset['multisignature']['keysgroup'];
        }

        $bytes .= UnsignedInteger::bit8($this->transaction->asset['multisignature']['min']);
        $bytes .= UnsignedInteger::bit8(count($this->transaction->asset['multisignature']['keysgroup']));
        $bytes .= UnsignedInteger::bit8($this->transaction->asset['multisignature']['lifetime']);
        $bytes .= hex2bin(implode('', $keysgroup));

        return $bytes;
    }
}
