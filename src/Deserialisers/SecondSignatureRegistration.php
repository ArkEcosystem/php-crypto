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

/**
 * This is the deserialiser class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class SecondSignatureRegistration extends Deserialiser
{
    /**
     * Handle the deserialisation of "delegate registration" data.
     *
     * @param int    $assetOffset
     * @param object $transaction
     *
     * @return object
     */
    public function handle(int $assetOffset, object $transaction): object
    {
        $transaction->asset = [
            'signature' => [
                'publicKey' => substr($this->hex, $assetOffset, 66),
            ],
        ];

        return $this->parseSignatures($transaction, $assetOffset + 66);
    }
}
