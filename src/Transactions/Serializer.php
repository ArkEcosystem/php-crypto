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

use BitWasp\Buffertools\Buffer;
use ArkEcosystem\Crypto\Configuration\Network;
use BrianFaust\Binary\Buffer\Writer\Buffer as Writer;

/**
 * This is the serializer class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class Serializer
{
    /**
     * The transaction serializers.
     *
     * @var array
     */
    private $serializers = [
        Serializers\Transfer::class,
        Serializers\SecondSignatureRegistration::class,
        Serializers\DelegateRegistration::class,
        Serializers\Vote::class,
        Serializers\MultiSignatureRegistration::class,
        Serializers\IPFS::class,
        Serializers\TimelockTransfer::class,
        Serializers\MultiPayment::class,
        Serializers\DelegateResignation::class,
    ];

    /**
     * Create a new serializer instance.
     *
     * @param \ArkEcosystem\Crypto\Transaction|array $transaction
     */
    private function __construct($transaction)
    {
        if ($transaction instanceof Transaction) {
            $transaction = $transaction->toArray();
        }

        $this->transaction = $transaction;
    }

    /**
     * Create a new deserializer instance.
     *
     * @param \ArkEcosystem\Crypto\Transaction|array $transaction
     */
    public static function new($transaction)
    {
        return new static($transaction);
    }

    /**
     * Perform AIP11 compliant serialization.
     *
     * @return \BitWasp\Buffertools\Buffer
     */
    public function serialize(): Buffer
    {
        $buffer = new Writer();
        $buffer->writeUInt8(0xff);
        $buffer->writeUInt8($this->transaction['version'] ?? 0x01);
        $buffer->writeUInt8($this->transaction['network'] ?? Network::version());
        $buffer->writeUInt8($this->transaction['type']);
        $buffer->writeUInt32($this->transaction['timestamp']);
        $buffer->writeHex($this->transaction['senderPublicKey']);
        $buffer->writeUInt64($this->transaction['fee']);

        if (isset($this->transaction['vendorField'])) {
            $vendorFieldLength = strlen($this->transaction['vendorField']);
            $buffer->writeUInt8($vendorFieldLength);
            $buffer->writeString($this->transaction['vendorField']);
        } elseif (isset($this->transaction['vendorFieldHex'])) {
            $vendorFieldHexLength = strlen($this->transaction['vendorFieldHex']);
            $buffer->writeUInt8($vendorFieldHexLength / 2);
            $buffer->writeHex($this->transaction['vendorFieldHex']);
        } else {
            $buffer->writeUInt8(0x00);
        }

        $this->handleType($buffer);
        $this->handleSignatures($buffer);

        return new Buffer($buffer->toBytes());
    }

    /**
     * Handle the serialization of transaction data.
     *
     * @param \BrianFaust\Binary\Buffer\Writer\Buffer $buffer
     *
     * @return string
     */
    public function handleType(Writer $buffer): void
    {
        $serializer = $this->serializers[$this->transaction['type']];

        (new $serializer($this->transaction, $buffer))->serialize();
    }

    /**
     * Handle the serialization of transaction data.
     *
     * @param \BrianFaust\Binary\Buffer\Writer\Buffer $buffer
     *
     * @return string
     */
    public function handleSignatures(Writer $buffer): void
    {
        if (isset($this->transaction['signature'])) {
            $buffer->writeHexBytes($this->transaction['signature']);
        }

        if (isset($this->transaction['secondSignature'])) {
            $buffer->writeHexBytes($this->transaction['secondSignature']);
        } elseif (isset($this->transaction['signSignature'])) {
            $buffer->writeHexBytes($this->transaction['signSignature']);
        }

        if (isset($this->transaction['signatures'])) {
            $buffer->writeUInt8(0xff);
            $buffer->writeHexBytes(implode('', $this->transaction['signatures']));
        }
    }
}
