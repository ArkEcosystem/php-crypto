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

use stdClass;

/**
 * This is the deserialiser class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class SecondSignatureRegistration extends AbstractDeserialiser
{
    /**
     * Handle the deserialisation of "delegate registration" data.
     *
     * @return object
     */
    public function deserialise(): object
    {
        $this->transaction->asset                       = new stdClass();
        $this->transaction->asset->signature            = new stdClass();
        $this->transaction->asset->signature->publicKey = substr($this->hex, $this->assetOffset, 66);

        return $this->parseSignatures($this->assetOffset + 66);
    }
}
