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

namespace ArkEcosystem\Crypto\Transactions\Types;

use BitWasp\Bitcoin\Base58;
use BitWasp\Buffertools\Buffer;
use Konceiver\ByteBuffer\ByteBuffer;

class HtlcLock extends Transaction
{
    public function serialize(array $options = []): ByteBuffer
    {
        $buffer = ByteBuffer::new(1);

        $buffer->writeUint64(+$this->data['amount']);
        $buffer->append(ByteBuffer::fromHex($this->data['asset']['lock']['secretHash']));
        $buffer->writeUint8(+$this->data['asset']['lock']['expiration']['type']);
        $buffer->writeUint32(+$this->data['asset']['lock']['expiration']['value']);
        $buffer->append(Base58::decodeCheck($this->data['recipientId'])->getBinary());

        return $buffer;
    }

    public function deserialize(ByteBuffer $buffer): void
    {
        $amount          = strval($buffer->readUint64());
        $secretHash      = $buffer->readHex(32 * 2);
        $expirationType  = $buffer->readUint8();
        $expirationValue = $buffer->readUint32();
        $recipientId     = Base58::encodeCheck(new Buffer($buffer->readHexString(21 * 2)));

        $this->data['amount']      = $amount;
        $this->data['recipientId'] = $recipientId;
        $this->data['asset']       = [
            'lock' => [
                'secretHash' => $secretHash,
                'expiration' => [
                    'type'  => $expirationType,
                    'value' => $expirationValue,
                ],
            ],
        ];
    }

    public function hasVendorField(): bool
    {
        return true;
    }
}
