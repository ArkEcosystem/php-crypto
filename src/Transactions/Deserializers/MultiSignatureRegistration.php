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
class MultiSignatureRegistration extends AbstractDeserializer
{
    /**
     * Handle the deserialization of "multi signature registration" data.
     *
     * @return object
     */
    public function deserialize(): object
    {
        $this->buffer->position($this->assetOffset / 2);

        $this->transaction->asset = [
            'multisignature' => [
                'min'      => $this->buffer->readUInt8() & 0xff,
                'lifetime' => $this->buffer->skip(1)->readUInt8() & 0xff,
            ],
        ];

        $count = $this->buffer->readUInt8() & 0xff;
        for ($i = 0; $i < $count; ++$i) {
            $indexStart = $this->assetOffset + 6;

            if ($i > 0) {
                $indexStart += $i * 66;
            }

            $this->transaction->asset['multisignature']['keysgroup'][] = $this->buffer->position($indexStart)->readHexRaw(66);
        }

        return $this->parseSignatures($this->assetOffset + 6 + $count * 66);
    }
}
