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
use BrianFaust\Binary\Binary;

/**
 * This is the serialiser class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class Serialiser
{
    /**
     * Create a new serialiser instance.
     *
     * @param object $transaction
     */
    private function __construct(object $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Create a new serialiser instance from an object.
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
     * Create a new serialiser instance from an array.
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
     * Create a new serialiser instance from a string.
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
     * Perform AIP11 compliant serialisation.
     *
     * @return \BitWasp\Buffertools\Buffer
     */
    public function serialise(): Buffer
    {
        $bytes = '';
        $bytes .= Binary::writeUInt8(0xff);
        $bytes .= Binary::writeLowNibbleHex($this->transaction->version ?? 0x01);
        $bytes .= Binary::writeUInt8($this->transaction->network ?? 0x23);
        $bytes .= Binary::writeLowNibbleHex($this->transaction->type);
        $bytes .= Binary::writeUInt32($this->transaction->timestamp);
        $bytes .= Binary::writeHighNibbleHex($this->transaction->senderPublicKey, strlen($this->transaction->senderPublicKey));
        $bytes .= Binary::writeUInt64($this->transaction->fee);

        if (isset($this->transaction->vendorField)) {
            $vendorFieldLength = strlen($this->transaction->vendorField);
            $bytes .= Binary::writeUInt8($vendorFieldLength);
            $bytes .= $this->transaction->vendorField;
        } elseif (isset($this->transaction->vendorFieldHex)) {
            $vendorFieldHexLength = strlen($this->transaction->vendorFieldHex);
            $bytes .= Binary::writeUInt8($vendorFieldHexLength / 2);
            $bytes .= $this->transaction->vendorFieldHex;
        } else {
            $bytes .= Binary::writeUInt8(0x00);
        }

        $bytes = $this->handleByType($bytes);

        if (isset($this->transaction->signature)) {
            $bytes .= hex2bin($this->transaction->signature);
        }

        if (isset($this->transaction->secondSignature)) {
            $bytes .= hex2bin($this->transaction->secondSignature);
        } elseif (isset($this->transaction->signSignature)) {
            $bytes .= hex2bin($this->transaction->signSignature);
        }

        if (isset($this->transaction->signatures)) {
            $bytes .= Binary::writeUInt8(0xff);
            $bytes .= hex2bin(implode('', $this->transaction->signatures));
        }

        return Buffer::hex(bin2hex($bytes));
    }

    /**
     * Handle the serialisation of transaction type specific data.
     *
     * @param string $bytes
     *
     * @return string
     */
    private function handleByType(string $bytes): string
    {
        if (Types::TRANSFER === $this->transaction->type) {
            return $this->handleTransfer($bytes);
        }

        if (Types::SECOND_SIGNATURE_REGISTRATION === $this->transaction->type) {
            return $this->handleSecondSignatureRegistration($bytes);
        }

        if (Types::DELEGATE_REGISTRATION === $this->transaction->type) {
            return $this->handleDelegateRegistration($bytes);
        }

        if (Types::VOTE === $this->transaction->type) {
            return $this->handleVote($bytes);
        }

        if (Types::MULTI_SIGNATURE_REGISTRATION === $this->transaction->type) {
            return $this->handleMultiSignatureRegistration($bytes);
        }

        if (Types::IPFS === $this->transaction->type) {
            return $this->handleIpfs($bytes);
        }

        if (Types::TIMELOCK_TRANSFER === $this->transaction->type) {
            return $this->handleTimelockTransfer($bytes);
        }

        if (Types::MULTI_PAYMENT === $this->transaction->type) {
            return $this->handleMultiPayment($bytes);
        }

        if (Types::DELEGATE_RESIGNATION === $this->transaction->type) {
            return $this->handleDelegateResignation($bytes);
        }
    }

    /**
     * Handle the serialisation of "transfer" data.
     *
     * @param string $bytes
     *
     * @return string
     */
    private function handleTransfer(string $bytes): string
    {
        $bytes .= Binary::writeUInt64($this->transaction->amount);
        $bytes .= Binary::writeUInt32($this->transaction->expiration ?? 0);

        $recipientId = Base58::decodeCheck($this->transaction->recipientId)->getHex();
        $bytes .= Binary::writeHighNibbleHex($recipientId, strlen($recipientId));

        return $bytes;
    }

    /**
     * Handle the serialisation of "delegate registration" data.
     *
     * @param string $bytes
     *
     * @return string
     */
    private function handleSecondSignatureRegistration(string $bytes): string
    {
        $bytes .= hex2bin($this->transaction->asset['signature']['publicKey']);

        return $bytes;
    }

    /**
     * Handle the serialisation of "vote" data.
     *
     * @param string $bytes
     *
     * @return string
     */
    private function handleDelegateRegistration(string $bytes): string
    {
        $delegateBytes = bin2hex($this->transaction->asset['delegate']['username']);
        $bytes .= Binary::writeUInt8(strlen($delegateBytes) / 2);
        $bytes .= hex2bin($delegateBytes);

        return $bytes;
    }

    /**
     * Handle the serialisation of "second signature registration" data.
     *
     * @param string $bytes
     *
     * @return string
     */
    private function handleVote(string $bytes): string
    {
        $voteBytes = [];

        foreach ($this->transaction->asset['votes'] as $vote) {
            $voteBytes[] = '+' === substr($vote, 0, 1)
                ? '01'.substr($vote, 1)
                : '00'.substr($vote, 1);
        }

        $bytes .= Binary::writeUInt8(count($this->transaction->asset['votes']));
        $bytes .= hex2bin(implode('', $voteBytes));

        return $bytes;
    }

    /**
     * Handle the serialisation of "multi signature registration" data.
     *
     * @param string $bytes
     *
     * @return string
     */
    private function handleMultiSignatureRegistration(string $bytes): string
    {
        $keysgroup = [];

        if (!isset($transaction->version) || 1 === $transaction->version) {
            foreach ($this->transaction->asset['multisignature']['keysgroup'] as $key) {
                $keysgroup[] = substr($key, 1);
            }
        } else {
            $keysgroup = $this->transaction->asset['multisignature']['keysgroup'];
        }

        $bytes .= Binary::writeUInt8($this->transaction->asset['multisignature']['min']);
        $bytes .= Binary::writeUInt8(count($this->transaction->asset['multisignature']['keysgroup']));
        $bytes .= Binary::writeUInt8($this->transaction->asset['multisignature']['lifetime']);
        $bytes .= hex2bin(implode('', $keysgroup));

        return $bytes;
    }

    /**
     * Handle the serialisation of "ipfs" data.
     *
     * @param string $bytes
     *
     * @return string
     */
    private function handleIpfs(string $bytes): string
    {
        $dag = $this->transaction->asset['ipfs']['dag'];

        $bytes .= Binary::writeUInt8(strlen($dag) / 2);
        $bytes .= hex2bin($dag);

        return $bytes;
    }

    /**
     * Handle the serialisation of "timelock transfer" data.
     *
     * @param string $bytes
     *
     * @return string
     */
    private function handleTimelockTransfer(string $bytes): string
    {
        $bytes .= Binary::writeUInt64($this->transaction->amount);
        $bytes .= Binary::writeLowNibbleHex($this->transaction->timelocktype);
        $bytes .= Binary::writeUInt32($this->transaction->timelock);

        $recipientId = Base58::decodeCheck($this->transaction->recipientId)->getHex();
        $bytes .= Binary::writeHighNibbleHex($recipientId, strlen($recipientId));

        return $bytes;
    }

    /**
     * Handle the serialisation of "multi payment" data.
     *
     * @param string $bytes
     *
     * @return string
     */
    private function handleMultiPayment(string $bytes): string
    {
        $bytes .= Binary::writeUInt32(count($this->transaction->asset['payments']));

        foreach ($this->transaction->asset['payments'] as $payment) {
            $bytes .= Binary::writeUInt64($payment->amount);

            $recipientId = Base58::decodeCheck($payment->recipientId)->getHex();
            $bytes .= Binary::writeHighNibbleHex($recipientId, strlen($recipientId));
        }

        return $bytes;
    }

    /**
     * Handle the serialisation of "delegate resignation" data.
     *
     * @param string $bytes
     *
     * @return string
     */
    private function handleDelegateResignation(string $bytes): string
    {
        return $bytes;
    }
}
