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

use BitWasp\Buffertools\Buffer;
use BrianFaust\Binary\Hex\Writer as Hex;
use BrianFaust\Binary\UnsignedInteger\Writer as UnsignedInteger;

/**
 * This is the serialiser class.
 *
 * @author Brian Faust <brian@ark.io>
 */
abstract class Serialiser
{
    /**
     * Create a new serialiser instance.
     *
     * @param object $transaction
     */
    public function __construct(object $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Perform AIP11 compliant serialisation.
     *
     * @return \BitWasp\Buffertools\Buffer
     */
    public function serialise(): Buffer
    {
        $bytes = '';
        $bytes .= UnsignedInteger::bit8(0xff);
        $bytes .= Hex::low($this->transaction->version ?? 0x01);
        $bytes .= UnsignedInteger::bit8($this->transaction->network ?? 0x23);
        $bytes .= Hex::low($this->transaction->type);
        $bytes .= UnsignedInteger::bit32($this->transaction->timestamp);
        $bytes .= Hex::high($this->transaction->senderPublicKey, strlen($this->transaction->senderPublicKey));
        $bytes .= UnsignedInteger::bit64($this->transaction->fee);

        if (isset($this->transaction->vendorField)) {
            $vendorFieldLength = strlen($this->transaction->vendorField);
            $bytes .= UnsignedInteger::bit8($vendorFieldLength);
            $bytes .= $this->transaction->vendorField;
        } elseif (isset($this->transaction->vendorFieldHex)) {
            $vendorFieldHexLength = strlen($this->transaction->vendorFieldHex);
            $bytes .= UnsignedInteger::bit8($vendorFieldHexLength / 2);
            $bytes .= $this->transaction->vendorFieldHex;
        } else {
            $bytes .= UnsignedInteger::bit8(0x00);
        }

        $bytes = $this->handle($bytes);

        if (isset($this->transaction->signature)) {
            $bytes .= hex2bin($this->transaction->signature);
        }

        if (isset($this->transaction->secondSignature)) {
            $bytes .= hex2bin($this->transaction->secondSignature);
        } elseif (isset($this->transaction->signSignature)) {
            $bytes .= hex2bin($this->transaction->signSignature);
        }

        if (isset($this->transaction->signatures)) {
            $bytes .= UnsignedInteger::bit8(0xff);
            $bytes .= hex2bin(implode('', $this->transaction->signatures));
        }

        return Buffer::hex(bin2hex($bytes));
    }

    /**
     * Handle the serialisation of transaction data.
     *
     * @param string $bytes
     *
     * @return string
     */
    abstract public function handle(string $bytes): string;
}
