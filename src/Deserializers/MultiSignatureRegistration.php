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

use BrianFaust\Binary\UnsignedInteger\Reader as UnsignedInteger;
use stdClass;

/**
 * This is the deserializer class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class MultiSignatureRegistration extends AbstractDeserializer
{
    /**
     * Handle the deserialisation of "multi signature registration" data.
     *
     * @return object
     */
    public function deserialize(): object
    {
        $this->transaction->asset                            = new stdClass();
        $this->transaction->asset->multisignature            = new stdClass();
        $this->transaction->asset->multisignature->keysgroup = [];

        $this->transaction->asset->multisignature->min      = UnsignedInteger::bit8($this->binary, $this->assetOffset / 2) & 0xff;
        $this->transaction->asset->multisignature->lifetime = UnsignedInteger::bit8($this->binary, $this->assetOffset / 2 + 2) & 0xff;

        $count = UnsignedInteger::bit8($this->binary, $this->assetOffset / 2 + 1) & 0xff;
        for ($i = 0; $i < $count; ++$i) {
            $indexStart = $this->assetOffset + 6;

            if ($i > 0) {
                $indexStart += $i * 66;
            }

            $this->transaction->asset->multisignature->keysgroup[] = substr($this->hex, $indexStart, 66);
        }

        return $this->parseSignatures($this->assetOffset + 6 + $count * 66);
    }
}
