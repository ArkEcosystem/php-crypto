<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Transactions;

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;
use ArkEcosystem\Crypto\Transactions\Types\AbstractTransaction;
use ArkEcosystem\Crypto\Utils\TransactionUtils;
use BitWasp\Buffertools\Buffer;

class Serializer
{
    public AbstractTransaction $transaction;

    /**
     * Create a new serializer instance.
     *
     * @param AbstractTransaction $transaction
     */
    private function __construct($transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Create a new deserializer instance.
     *
     * @param AbstractTransaction $transaction
     */
    public static function new($transaction)
    {
        return new static($transaction);
    }

    public static function getBytes(AbstractTransaction $transaction, bool $skipSignature = false): Buffer
    {
        return $transaction->serialize(skipSignature: $skipSignature);
    }

    /**
     * @return Buffer
     */
    public function serialize(bool $skipSignature = false): Buffer
    {
        $this->transaction->serialized = TransactionUtils::toBuffer($this->transaction->data, $skipSignature);

        return $this->transaction->serialized;
    }

    /*
     * Handle the serialization of transaction data.
     *
     * @return string
     */
    // private function serializeSignatures(ByteBuffer $buffer, bool $skipSignature = false): void
    // {
    //     if (! $skipSignature && isset($this->transaction->data['signature'])) {
    //         $buffer->writeHex($this->transaction->data['signature']);
    //     }
    // }
}
