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

use ArkEcosystem\Crypto\Transactions\Enums\Types;
use BitWasp\Bitcoin\Base58;
use BitWasp\Buffertools\Buffer;
use BrianFaust\Binary\Hex\Reader as Hex;
use BrianFaust\Binary\UnsignedInteger\Reader as UnsignedInteger;
use stdClass;

/**
 * This is the deserialiser class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class Deserialiser
{
    /**
     * Create a new deserialiser instance.
     *
     * @param object $transaction
     */
    private function __construct(object $transaction)
    {
        $this->transaction = $transaction;

        $buffer       = new Buffer($transaction->serialized);
        $this->binary = $buffer->getBinary();
        $this->hex    = $buffer->getHex();
    }

    /**
     * Create a new deserialiser instance.
     *
     * @param object $transaction
     *
     * @return object
     */
    public static function fromObject(object $transaction): self
    {
        return new static($transaction);
    }

    /**
     * Create a new deserialiser instance.
     *
     * @param array $transaction
     *
     * @return object
     */
    public static function fromArray(array $transaction): self
    {
        return $this->fromString(json_encode($transaction));
    }

    /**
     * Create a new deserialiser instance.
     *
     * @param string $transaction
     *
     * @return object
     */
    public static function fromString(string $transaction): self
    {
        return new static(json_decode($transaction));
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

        $transaction = $this->handleByType($assetOffset, $transaction);

        if (!isset($transaction->amount)) {
            $transaction->amount = 0;
        }

        if (!isset($transaction->version) || 1 === $transaction->version) {
            if (isset($transaction->secondSignature)) {
                $transaction->signSignature = $transaction->secondSignature;
            }

            if ($this->transaction->is_vote) {
                $transaction->recipientId = Crypto::addressFromPublicKey($this->transaction->senderPublicKey);
            }

            if ($this->transaction->is_second_signature) {
                $transaction->recipientId = Crypto::addressFromPublicKey($this->transaction->senderPublicKey);
            }

            if ($this->transaction->is_multi_signature) {
                $transaction->recipientId = Crypto::addressFromPublicKey($this->transaction->senderPublicKey);

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
     * Handle the deserialisation of transaction type specific data.
     *
     * @param int    $assetOffset
     * @param object $transaction
     *
     * @return object
     */
    private function handleByType(int $assetOffset, object $transaction): object
    {
        if (Types::TRANSFER === (int) $this->transaction->type) {
            return $this->handleTransfer($assetOffset, $transaction);
        }

        if (Types::SECOND_SIGNATURE_REGISTRATION === (int) $this->transaction->type) {
            return $this->handleSecondSignatureRegistration($assetOffset, $transaction);
        }

        if (Types::DELEGATE_REGISTRATION === (int) $this->transaction->type) {
            return $this->handleDelegateRegistration($assetOffset, $transaction);
        }

        if (Types::VOTE === (int) $this->transaction->type) {
            return $this->handleVote($assetOffset, $transaction);
        }

        if (Types::MULTI_SIGNATURE_REGISTRATION === (int) $this->transaction->type) {
            return $this->handleMultiSignatureRegistration($assetOffset, $transaction);
        }

        if (Types::IPFS === (int) $this->transaction->type) {
            return $this->handleIpfs($assetOffset, $transaction);
        }

        if (Types::TIMELOCK_TRANSFER === (int) $this->transaction->type) {
            return $this->handleTimelockTransfer($assetOffset, $transaction);
        }

        if (Types::MULTI_PAYMENT === (int) $this->transaction->type) {
            return $this->handleMultiPayment($assetOffset, $transaction);
        }

        if (Types::DELEGATE_RESIGNATION === (int) $this->transaction->type) {
            return $this->handleDelegateResignation($assetOffset, $transaction);
        }
    }

    /**
     * Handle the deserialisation of "transfer" data.
     *
     * @param int    $assetOffset
     * @param object $transaction
     *
     * @return object
     */
    private function handleTransfer(int $assetOffset, object $transaction): object
    {
        $transaction->amount      = UnsignedInteger::bit64($this->binary, $assetOffset / 2);
        $transaction->expiration  = UnsignedInteger::bit32($this->binary, $assetOffset / 2 + 8);
        $transaction->recipientId = Hex::high($this->binary, $assetOffset / 2 + 12, 42);
        $transaction->recipientId = Base58::encodeCheck(new Buffer(hex2bin($transaction->recipientId)));

        return $this->parseSignatures($transaction, $assetOffset + (21 + 12) * 2);
    }

    /**
     * Handle the deserialisation of "delegate registration" data.
     *
     * @param int    $assetOffset
     * @param object $transaction
     *
     * @return object
     */
    private function handleSecondSignatureRegistration(int $assetOffset, object $transaction): object
    {
        $transaction->asset = [
            'signature' => [
                'publicKey' => substr($this->hex, $assetOffset, 66),
            ],
        ];

        return $this->parseSignatures($transaction, $assetOffset + 66);
    }

    /**
     * Handle the deserialisation of "vote" data.
     *
     * @param int    $assetOffset
     * @param object $transaction
     *
     * @return object
     */
    private function handleDelegateRegistration(int $assetOffset, object $transaction): object
    {
        $usernameLength = UnsignedInteger::bit8($this->binary, $assetOffset / 2) & 0xff;

        $transaction->asset = [
            'delegate' => [
                'username' => hex2bin(substr($this->hex, $assetOffset + 2, $usernameLength * 2)),
            ],
        ];

        return $this->parseSignatures($transaction, $assetOffset + ($usernameLength + 1) * 2);
    }

    /**
     * Handle the deserialisation of "second signature registration" data.
     *
     * @param int    $assetOffset
     * @param object $transaction
     *
     * @return object
     */
    private function handleVote(int $assetOffset, object $transaction): object
    {
        $voteLength = UnsignedInteger::bit8($this->binary, $assetOffset / 2) & 0xff;

        $transaction->asset = ['votes' => []];

        $vote = null;
        for ($i = 0; $i < $voteLength; ++$i) {
            $vote                            = substr($this->hex, $assetOffset + 2 + $i * 2 * 34, 2 * 34);
            $vote                            = ('1' === $vote[1] ? '+' : '-').substr($vote, 2);
            $transaction->asset['votes'][]   = $vote;
        }

        return $this->parseSignatures($transaction, $assetOffset + 2 + $voteLength * 34 * 2);
    }

    /**
     * Handle the deserialisation of "multi signature registration" data.
     *
     * @param int    $assetOffset
     * @param object $transaction
     *
     * @return object
     */
    private function handleMultiSignatureRegistration(int $assetOffset, object $transaction): object
    {
        $transaction->asset = [
            'multisignature' => [
                'keysgroup' => [],
            ],
        ];

        $transaction->asset['multisignature']['min']      = UnsignedInteger::bit8($this->binary, $assetOffset / 2) & 0xff;
        $num                                              = UnsignedInteger::bit8($this->binary, $assetOffset / 2 + 1) & 0xff;
        $transaction->asset['multisignature']['lifetime'] = UnsignedInteger::bit8($this->binary, $assetOffset / 2 + 2) & 0xff;

        for ($i = 0; $i < $num; ++$i) {
            $indexStart = $assetOffset + 6;

            if ($i > 0) {
                $indexStart += $i * 66;
            }

            $transaction->asset['multisignature']['keysgroup'][] = substr($this->hex, $indexStart, 66);
        }

        return $this->parseSignatures($transaction, $assetOffset + 6 + $num * 66);
    }

    /**
     * Handle the deserialisation of "ipfs" data.
     *
     * @param int    $assetOffset
     * @param object $transaction
     *
     * @return object
     */
    private function handleIpfs(int $assetOffset, object $transaction): object
    {
        $transaction->asset = [];

        $length                    = UnsignedInteger::bit8($this->binary, $assetOffset / 2) & 0xff;
        $transaction->asset['dag'] = substr($this->hex, $assetOffset + 2, $length * 2);

        return $this->parseSignatures($transaction, $assetOffset + 2 + $length * 2);
    }

    /**
     * Handle the deserialisation of "timelock transfer" data.
     *
     * @param int    $assetOffset
     * @param object $transaction
     *
     * @return object
     */
    private function handleTimelockTransfer(int $assetOffset, object $transaction): object
    {
        $transaction->amount       = UnsignedInteger::bit64($this->binary, $assetOffset / 2);
        $transaction->timelocktype = UnsignedInteger::bit8($this->binary, $assetOffset / 2 + 8) & 0xff;
        $transaction->timelock     = UnsignedInteger::bit32($this->binary, $assetOffset / 2 + 9);
        $transaction->recipientId  = Hex::high($this->binary, $assetOffset / 2 + 13, 42);
        $transaction->recipientId  = Base58::encodeCheck(new Buffer(hex2bin($transaction->recipientId)));

        return $this->parseSignatures($transaction, $assetOffset + (21 + 13) * 2);
    }

    /**
     * Handle the deserialisation of "multi payment" data.
     *
     * @param int    $assetOffset
     * @param object $transaction
     *
     * @return object
     */
    private function handleMultiPayment(int $assetOffset, object $transaction): object
    {
        $transaction->asset = [
            'payments' => [],
        ];

        $total  = UnsignedInteger::bit8($this->binary, $assetOffset / 2)[1] & 0xff;
        $offset = $assetOffset / 2 + 1;

        for ($i = 0; $i < $total; ++$i) {
            $payment              = new stdClass();
            $payment->amount      = UnsignedInteger::bit64($this->binary, $offset);
            $payment->recipientId = Hex::high($this->binary, $offset + 1, 42);
            $payment->recipientId = Base58::encodeCheck(new Buffer(hex2bin($payment['recipientId'])));

            $transaction->asset['payments'][] = $payment;

            $offset += 22;
        }

        $transaction->amount = array_sum(array_column($transaction->asset['payments'], 'amount'));

        return $this->parseSignatures($transaction, $offset * 2);
    }

    /**
     * Handle the deserialisation of "delegate resignation" data.
     *
     * @param int    $assetOffset
     * @param object $transaction
     *
     * @return object
     */
    private function handleDelegateResignation(int $assetOffset, object $transaction): object
    {
        return $this->parseSignatures($transaction, $assetOffset);
    }

    /**
     * [parseSignatures description].
     *
     * @param object $transaction
     * @param int    $startOffset
     *
     * @return object
     */
    private function parseSignatures(object $transaction, int $startOffset): object
    {
        $transaction->signature = substr($this->hex, $startOffset);

        $multiSignatureOffset = 0;

        if (0 === strlen($transaction->signature)) {
            unset($transaction->signature);
        } else {
            $length1                = intval(substr($transaction->signature, 2, 2), 16) + 2;
            $transaction->signature = substr($this->hex, $startOffset, $length1 * 2);
            $multiSignatureOffset += $length1 * 2;
            $transaction->secondSignature = substr($this->hex, $startOffset + $length1 * 2);

            if (0 === strlen($transaction->secondSignature)) {
                unset($transaction->secondSignature);
            } else {
                if ('ff' === substr($transaction->secondSignature, 0, 2)) { // start of multi-signature
                    unset($transaction->secondSignature);
                } else {
                    $length2                      = intval(substr($transaction->secondSignature, 2, 2), 16) + 2;
                    $transaction->secondSignature = substr($transaction->secondSignature, 0, $length2 * 2);
                    $multiSignatureOffset += $length2 * 2;
                }
            }

            $signatures = substr($this->hex, $startOffset + $multiSignatureOffset);

            if (0 === strlen($signatures)) {
                return $transaction;
            }

            if ('ff' !== substr($signatures, 0, 2)) {
                return $transaction;
            }

            $signatures              = substr($signatures, 2);
            $transaction->signatures = [];

            $moreSignatures = true;
            while ($moreSignatures) {
                $mLength = intval(substr($signatures, 2, 2), 16);

                if ($mLength > 0) {
                    $transaction->signatures[] = substr($signatures, 0, ($mLength + 2) * 2);
                } else {
                    $moreSignatures = false;
                }

                $signatures = substr($signatures, ($mLength + 2) * 2);
            }
        }

        return $transaction;
    }
}
