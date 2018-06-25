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

use ArkEcosystem\Crypto\Crypto;
use ArkEcosystem\Crypto\Identity\Address;
use BitWasp\Buffertools\Buffer;
use BrianFaust\Binary\Hex\Reader as Hex;
use BrianFaust\Binary\UnsignedInteger\Reader as UnsignedInteger;
use stdClass;

/**
 * This is the deserialiser class.
 *
 * @author Brian Faust <brian@ark.io>
 */
abstract class Deserialiser
{
    /**
     * Create a new deserialiser instance.
     *
     * @param object $transaction
     */
    public function __construct(object $transaction)
    {
        $this->transaction = $transaction;

        $buffer       = new Buffer($transaction->serialized);
        $this->binary = $buffer->getBinary();
        $this->hex    = $buffer->getHex();
    }

    /**
     * Perform AIP11 compliant deserialisation.
     *
     * @return stdClass
     */
    public function deserialise(): stdClass
    {
        $transaction                  = new stdClass();
        $transaction->id              = $this->transaction->id;
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

        $transaction = $this->handle($assetOffset, $transaction);

        if (!isset($transaction->amount)) {
            $transaction->amount = 0;
        }

        if (!isset($transaction->version) || 1 === $transaction->version) {
            if (isset($transaction->secondSignature)) {
                $transaction->signSignature = $transaction->secondSignature;
            }

            if ($this->transaction->is_vote) {
                $transaction->recipientId = Address::fromPublicKey($this->transaction->senderPublicKey);
            }

            if ($this->transaction->is_second_signature) {
                $transaction->recipientId = Address::fromPublicKey($this->transaction->senderPublicKey);
            }

            if ($this->transaction->is_multi_signature) {
                $transaction->recipientId = Address::fromPublicKey($this->transaction->senderPublicKey);

                $transaction->asset['multisignature']['keysgroup'] = array_map(function ($key) {
                    return '+'.$key;
                }, $transaction->asset['multisignature']['keysgroup']);
            }

            if (isset($transaction->vendorFieldHex)) {
                $transaction->vendorField = hex2bin($transaction->vendorFieldHex);
            }
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
    abstract public function handle(int $assetOffset, object $transaction): object;

    /**
     * Parse the signatures of the given transaction.
     *
     * @param object $transaction
     * @param int    $startOffset
     *
     * @return object
     */
    protected function parseSignatures(object $transaction, int $startOffset): object
    {
        return Crypto::parseSignatures($this->hex, $transaction, $startOffset);
    }
}
