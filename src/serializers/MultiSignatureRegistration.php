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
class MultiSignatureRegistration extends AbstractSerializer
{
    /**
     * Handle the serialisation of "multi signature registration" data.
     *
     * @return string
     */
    public function serialize(): string
    {
        $keysgroup = [];

        if (!isset($this->transaction->version) || 1 === $this->transaction->version) {
            foreach ($this->transaction->asset->multisignature->keysgroup as $key) {
                $keysgroup[] = '+' === substr($key, 0, 1)
                    ? substr($key, 1)
                    : $key;
            }
        } else {
            $keysgroup = $this->transaction->asset->multisignature->keysgroup;
        }

        $this->bytes .= UnsignedInteger::bit8($this->transaction->asset->multisignature->min);
        $this->bytes .= UnsignedInteger::bit8(count($this->transaction->asset->multisignature->keysgroup));
        $this->bytes .= UnsignedInteger::bit8($this->transaction->asset->multisignature->lifetime);
        $this->bytes .= hex2bin(implode('', $keysgroup));

        return $this->bytes;
    }
}
