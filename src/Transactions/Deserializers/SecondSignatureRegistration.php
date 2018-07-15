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

/**
 * This is the deserializer class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class SecondSignatureRegistration extends AbstractDeserializer
{
    /**
     * Handle the deserialisation of "delegate registration" data.
     *
     * @return object
     */
    public function deserialize(): object
    {
        $this->buffer->position($this->assetOffset);

        $this->transaction->asset = [
            'signature' => [
                'publicKey' => $this->buffer->readHexRaw(66),
            ],
        ];

        return $this->parseSignatures($this->assetOffset + 66);
    }
}
