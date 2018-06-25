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

use BrianFaust\Binary\UnsignedInteger\Reader as UnsignedInteger;
use stdClass;

/**
 * This is the deserialiser class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class MultiSignatureRegistration extends AbstractDeserialiser
{
    /**
     * Handle the deserialisation of "multi signature registration" data.
     *
     * @param int    $assetOffset
     * @param object $transaction
     *
     * @return object
     */
    public function handle(int $assetOffset, object $transaction): object
    {
        $transaction->asset                            = new stdClass();
        $transaction->asset->multisignature            = new stdClass();
        $transaction->asset->multisignature->keysgroup = [];

        $transaction->asset->multisignature->min      = UnsignedInteger::bit8($this->binary, $assetOffset / 2) & 0xff;
        $transaction->asset->multisignature->lifetime = UnsignedInteger::bit8($this->binary, $assetOffset / 2 + 2) & 0xff;

        $count = UnsignedInteger::bit8($this->binary, $assetOffset / 2 + 1) & 0xff;
        for ($i = 0; $i < $count; ++$i) {
            $indexStart = $assetOffset + 6;

            if ($i > 0) {
                $indexStart += $i * 66;
            }

            $transaction->asset->multisignature->keysgroup[] = substr($this->hex, $indexStart, 66);
        }

        return $this->parseSignatures($transaction, $assetOffset + 6 + $count * 66);
    }
}
