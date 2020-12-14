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

namespace ArkEcosystem\Crypto\Transactions;

use ArkEcosystem\Crypto\Configuration\Network;
use ArkEcosystem\Crypto\Enums\TypeGroup;
use ArkEcosystem\Crypto\Transactions\Types\Transaction;
use BitWasp\Buffertools\Buffer;
use Konceiver\ByteBuffer\ByteBuffer;

/**
 * This is the serializer class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class Serializer
{
    /**
     * Create a new serializer instance.
     *
     * @param \ArkEcosystem\Crypto\Transactions\Types\Transaction $transaction
     */
    private function __construct($transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Create a new deserializer instance.
     *
     * @param \ArkEcosystem\Crypto\Transactions\Types\Transaction $transaction
     */
    public static function new($transaction)
    {
        return new static($transaction);
    }

    public static function getBytes(Transaction $transaction, array $options = []): Buffer
    {
        return self::new($transaction)->serialize($options);
    }

    /**
     * Perform AIP11 compliant serialization.
     *
     * @return \BitWasp\Buffertools\Buffer
     */
    public function serialize(array $options = []): Buffer
    {
        $buffer = ByteBuffer::new(1); // initialize with size 1, size will expand as we add bytes

        $this->serializeCommon($buffer);

        $this->serializeVendorField($buffer);

        $typeBuffer = $this->transaction->serialize($options);
        $buffer->append($typeBuffer);

        $this->serializeSignatures($buffer, $options);

        return new Buffer($buffer->toString('binary'));
    }

    private function serializeCommon(ByteBuffer $buffer): void
    {
        $this->transaction->data['version'] = $this->transaction->data['version'] ?? 0x01;
        if (! isset($this->transaction->data['typeGroup'])) {
            $this->transaction->data['typeGroup'] = TypeGroup::CORE;
        }

        $buffer->writeByte(0xff);
        $buffer->writeByte($this->transaction->data['version']);
        $buffer->writeByte($this->transaction->data['network'] ?? Network::version());

        if ($this->transaction->data['version'] === 1) {
            $buffer->writeByte($this->transaction->data['type']);
            $buffer->writeUint32($this->transaction->data['timestamp']);
        } else {
            $buffer->writeUint32($this->transaction->data['typeGroup']);
            $buffer->writeUint16($this->transaction->data['type']);
            $buffer->writeUint64(+$this->transaction->data['nonce']);
        }

        $buffer->writeHex($this->transaction->data['senderPublicKey']);
        $buffer->writeUint64(+$this->transaction->data['fee']);
    }

    private function serializeVendorField(ByteBuffer $buffer): void
    {
        if ($this->transaction->hasVendorField()) {
            $data = $this->transaction->data;

            if (isset($data['vendorField'])) {
                $vendorFieldLength = strlen($data['vendorField']);
                $buffer->writeUInt8($vendorFieldLength);
                $buffer->writeString($data['vendorField']);
            } elseif (isset($data['vendorFieldHex'])) {
                $vendorFieldHexLength = strlen($data['vendorFieldHex']);
                $buffer->writeUInt8($vendorFieldHexLength / 2);
                $buffer->writeHex($data['vendorFieldHex']);
            } else {
                $buffer->writeUInt8(0x00);
            }
        } else {
            $buffer->writeUInt8(0x00);
        }
    }

    /**
     * Handle the serialization of transaction data.
     *
     * @param ByteBuffer $buffer
     *
     * @return string
     */
    public function serializeSignatures(ByteBuffer $buffer, array $options): void
    {
        $skipSignature       = $options['skipSignature'] ?? false;
        $skipSecondSignature = $options['skipSecondSignature'] ?? false;
        $skipMultiSignature  = $options['skipMultiSignature'] ?? false;

        if (! $skipSignature && isset($this->transaction->data['signature'])) {
            $buffer->writeHex($this->transaction->data['signature']);
        }

        if (! $skipSecondSignature) {
            if (isset($this->transaction->data['secondSignature'])) {
                $buffer->writeHex($this->transaction->data['secondSignature']);
            }
        }

        if (! $skipMultiSignature && isset($this->transaction->data['signatures'])) {
            $buffer->writeHex(implode('', $this->transaction->data['signatures']));
        }
    }
}
