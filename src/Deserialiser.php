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
 * This is the deserialiser class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class Deserialiser
{
    /**
     * The transaction deserialisers.
     *
     * @var array
     */
    private $deserialisers = [
        Deserialisers\Transfer::class,
        Deserialisers\SecondSignatureRegistration::class,
        Deserialisers\DelegateRegistration::class,
        Deserialisers\Vote::class,
        Deserialisers\MultiSignatureRegistration::class,
        Deserialisers\IPFS::class,
        Deserialisers\TimelockTransfer::class,
        Deserialisers\MultiPayment::class,
        Deserialisers\DelegateResignation::class,
    ];

    /**
     * Create a new deserialiser instance.
     *
     * @param object $serialised
     */
    public function __construct(string $serialised)
    {
        $buffer = false === strpos($serialised, "\0")
            ? Buffer::hex($serialised)
            : new Buffer($serialised);

        $this->binary = $buffer->getBinary();
        $this->hex    = $buffer->getHex();
    }

    /**
     * Create a new deserialiser instance.
     *
     * @param string $serialised
     */
    public static function new(string $serialised)
    {
        return new static($serialised);
    }

    /**
     * Perform AIP11 compliant deserialisation.
     *
     * @return \ArkEcosystem\Crypto\Transaction
     */
    public function deserialise(): Transaction
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
     * @param int    $assetOffset
     * @param object $transaction
     *
     * @return object
     */
    public function handleType(int $assetOffset, object $transaction): object
    {
        $deserialiser = $this->deserialisers[$transaction->type];

        return (new $deserialiser($this->hex, $this->binary, $assetOffset, $transaction))->deserialise();
    }

    /**
     * Handle the deserialisation of transaction data.
     *
     * @param object $transaction
     *
     * @return object
     */
    public function handleVersionOne(object $transaction): object
    {
        if (isset($transaction->secondSignature)) {
            $transaction->signSignature = $transaction->secondSignature;
        }

        if (Types::VOTE === $transaction->type) {
            $transaction->recipientId = Address::fromPublicKey($transaction->senderPublicKey, $transaction->network);
        }

        if (Types::SECOND_SIGNATURE_REGISTRATION === $transaction->type) {
            $transaction->recipientId = Address::fromPublicKey($transaction->senderPublicKey, $transaction->network);
        }

        if (Types::MULTI_SIGNATURE_REGISTRATION === $transaction->type) {
            // The "recipientId" doesn't exist on v1 multi signature registrations
            // $transaction->recipientId = Address::fromPublicKey($transaction->senderPublicKey, $transaction->network);

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

        return $transaction;
    }
}
