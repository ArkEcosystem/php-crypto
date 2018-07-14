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

/**
 * This is the serializer class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class DelegateRegistration extends AbstractSerializer
{
    /**
     * Handle the serialisation of "vote" data.
     *
     * @return string
     */
    public function serialize(): void
    {
        $delegateBytes = bin2hex($this->transaction['asset']['delegate']['username']);

        $this->buffer->writeUInt8(strlen($delegateBytes) / 2);
        $this->buffer->writeHexBytes($delegateBytes);
    }
}
