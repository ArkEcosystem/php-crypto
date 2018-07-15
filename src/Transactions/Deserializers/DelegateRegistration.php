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
class DelegateRegistration extends AbstractDeserializer
{
    /**
     * Handle the deserialization of "vote" data.
     *
     * @return object
     */
    public function deserialize(): object
    {
        $this->buffer->position($this->assetOffset / 2);

        $usernameLength = $this->buffer->readUInt8();

        $this->transaction->asset = [
            'delegate' => [
                'username' => $this->buffer->position($this->assetOffset + 2)->readHexBytes($usernameLength * 2),
            ],
        ];

        return $this->parseSignatures($this->assetOffset + ($usernameLength + 1) * 2);
    }
}
