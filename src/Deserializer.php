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

namespace ArkEcosystem\Crypto;

use ArkEcosystem\Crypto\Enums\Types;
use ArkEcosystem\Crypto\Identity\Address;
use BitWasp\Buffertools\Buffer;
use BrianFaust\Binary\Hex\Reader as Hex;
use BrianFaust\Binary\UnsignedInteger\Reader as UnsignedInteger;

/**
 * This is the deserializer class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class Deserializer
{
    /**
     * The transaction deserializers.
     *
     * @var array
     */
    private $deserializers = [
        Deserializers\Transfer::class,
        Deserializers\SecondSignatureRegistration::class,
        Deserializers\DelegateRegistration::class,
        Deserializers\Vote::class,
        Deserializers\MultiSignatureRegistration::class,
        Deserializers\IPFS::class,
        Deserializers\TimelockTransfer::class,
        Deserializers\MultiPayment::class,
        Deserializers\DelegateResignation::class,
    ];

    /**
     * Create a new deserializer instance.
     *
     * @param object $serialized
     */
    public function __construct(string $serialized)
    {
        $buffer = false === strpos($serialized, "\0")
            ? Buffer::hex($serialized)
            : new Buffer($serialized);

        $this->binary = $buffer->getBinary();
        $this->hex    = $buffer->getHex();
    }

    /**
     * Create a new deserializer instance.
     *
     * @param string $serialized
     */
    public static function new(string $serialized)
    {
        return new static($serialized);
    }

    /**
     * Perform AIP11 compliant deserialisation.
     *
     * @return \ArkEcosystem\Crypto\Transaction
     */
    public function deserialize(): Transaction
    {
        $transaction                  = new Transaction();
        $transaction->version         = (int) Hex::low($this->binary, 1);
        $transaction->network         = UnsignedInteger::bit8($this->binary, 2);
        $transaction->type            = UnsignedInteger::bit8($this->binary, 3);
        $transaction->timestamp       = UnsignedInteger::bit32($this->binary, 4);
        $transaction->senderPublicKey = Hex::high($this->binary, 8, 66);
        $transaction->fee             = UnsignedInteger::bit32($this->binary, 41);

        $vendorFieldLength = UnsignedInteger::bit8($this->binary, 41 + 8);
        if ($vendorFieldLength > 0) {
            $vendorFieldOffset             = $vendorFieldLength * 2;
            $transaction->vendorFieldHex   = Hex::high($this->binary, 41 + 8 + 1, $vendorFieldOffset);
        }

        $assetOffset = (41 + 8 + 1) * 2 + $vendorFieldLength * 2;

        $transaction = $this->handleType($assetOffset, $transaction);

        if (!isset($transaction->amount)) {
            $transaction->amount = 0;
        }

        if (!isset($transaction->version) || 1 === $transaction->version) {
            $transaction = $this->handleVersionOne($transaction);
        }

        return $transaction;
    }

    /**
     * Handle the deserialisation of transaction data.
     *
     * @param int                              $assetOffset
     * @param \ArkEcosystem\Crypto\Transaction $transaction
     *
     * @return \ArkEcosystem\Crypto\Transaction
     */
    public function handleType(int $assetOffset, Transaction $transaction): Transaction
    {
        $deserializer = $this->deserializers[$transaction->type];

        return (new $deserializer($this->hex, $this->binary, $assetOffset, $transaction))->deserialize();
    }

    /**
     * Handle the deserialisation of transaction data.
     *
     * @param \ArkEcosystem\Crypto\Transaction $transaction
     *
     * @return \ArkEcosystem\Crypto\Transaction
     */
    public function handleVersionOne(Transaction $transaction): Transaction
    {
        if (isset($transaction->secondSignature)) {
            $transaction->signSignature = $transaction->secondSignature;
        }

        if (Types::VOTE === $transaction->type) {
            $transaction->recipientId = Address::fromPublicKey($transaction->senderPublicKey, $transaction->network);
        }

        if (Types::MULTI_SIGNATURE_REGISTRATION === $transaction->type) {
            $transaction->asset->multisignature->keysgroup = array_map(function ($key) {
                return '+'.$key;
            }, $transaction->asset->multisignature->keysgroup);
        }

        if (isset($transaction->vendorFieldHex)) {
            $transaction->vendorField = hex2bin($transaction->vendorFieldHex);
        }

        if (!isset($transaction->id)) {
            $transaction->id = $transaction->getId();
        }

        if (Types::SECOND_SIGNATURE_REGISTRATION === $transaction->type) {
            $transaction->recipientId = Address::fromPublicKey($transaction->senderPublicKey, $transaction->network);
        }

        if (Types::MULTI_SIGNATURE_REGISTRATION === $transaction->type) {
            $transaction->recipientId = Address::fromPublicKey($transaction->senderPublicKey, $transaction->network);
        }

        return $transaction;
    }
}
