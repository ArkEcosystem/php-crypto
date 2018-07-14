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
use BitWasp\Bitcoin\Crypto\Hash;
use BitWasp\Buffertools\Buffer;
use BrianFaust\Binary\Buffer\Reader\Buffer as Reader;
use BrianFaust\Binary\Hex\Reader as Hex;

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

        $this->buffer = Reader::fromHex($buffer->getHex())->skip(1);
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
        $transaction->version         = $this->buffer->readUInt8();
        $transaction->network         = $this->buffer->readUInt8();
        $transaction->type            = $this->buffer->readUInt8();
        $transaction->timestamp       = $this->buffer->readUInt32();
        $transaction->senderPublicKey = $this->buffer->readHex(33);
        $transaction->fee             = $this->buffer->readUInt64();

        $vendorFieldLength = $this->buffer->readUInt8();
        if ($vendorFieldLength > 0) {
            $transaction->vendorFieldHex = $this->buffer->readHex($vendorFieldLength);
        }

        $assetOffset = (41 + 8 + 1) * 2 + $vendorFieldLength * 2;

        $transaction = $this->handleType($assetOffset, $transaction);

        if (!isset($transaction->amount)) {
            $transaction->amount = 0;
        }

        if (!isset($transaction->version) || 1 === $transaction->version) {
            $transaction = $this->handleVersionOne($transaction);
        }

        if (2 === $transaction->version) {
            $transaction = $this->handleVersionTwo($transaction);
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

        return (new $deserializer($this->buffer, $assetOffset, $transaction))->deserialize();
    }

    /**
     * Handle the deserialisation of transaction data with a version of 1.0.
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
            $transaction->asset['multisignature']['keysgroup'] = array_map(function ($key) {
                return '+'.$key;
            }, $transaction->asset['multisignature']['keysgroup']);
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

    /**
     * Handle the deserialisation of transaction data with a version of 2.0.
     *
     * @param \ArkEcosystem\Crypto\Transaction $transaction
     *
     * @return \ArkEcosystem\Crypto\Transaction
     */
    public function handleVersionTwo(Transaction $transaction): Transaction
    {
        $transaction->id = Hash::sha256($transaction->serialize())->getHex();

        return $transaction;
    }
}
