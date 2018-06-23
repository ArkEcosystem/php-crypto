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
        $bytes .= pack('C', 0xff);
        $bytes .= pack('h', $this->transaction->version ?? 0x01);
        $bytes .= pack('C', $this->transaction->network ?? 0x30);
        $bytes .= pack('h', $this->transaction->type);
        $bytes .= pack('V', $this->transaction->timestamp);
        $bytes .= pack('H'.strlen($this->transaction->senderPublicKey), $this->transaction->senderPublicKey);
        $bytes .= pack('P', $this->transaction->fee);

        if (isset($this->transaction->vendorField)) {
            $vendorFieldLength = strlen($this->transaction->vendorField);
            $bytes .= pack('C', $vendorFieldLength);
            $bytes .= $this->transaction->vendorField;
        } elseif (isset($this->transaction->vendorFieldHex)) {
            $vendorFieldHexLength = strlen($this->transaction->vendorFieldHex);
            $bytes .= pack('C', $vendorFieldHexLength / 2);
            $bytes .= $this->transaction->vendorFieldHex;
        } else {
            $bytes .= pack('C', 0x00);
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

        if (isset($transaction->signatures)) {
            $bytes .= hex2bin(0xff);
            $bytes .= hex2bin(implode('', $transaction->signatures));
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
        $bytes .= pack('P', $this->transaction->amount);
        $bytes .= pack('V', $this->transaction->expiration ?? 0);

        $recipientId = Base58::decodeCheck($this->transaction->recipientId)->getHex();
        $bytes .= pack('H'.strlen($recipientId), $recipientId);

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
            $voteBytes[] = '+' === substr($vote, 0)
                ? '01'.substr($vote, 1)
                : '00'.substr($vote, 1);
        }

        $bytes .= pack('C', count($this->transaction->asset['votes']));
        $bytes .= hex2bin(implode('', $voteBytes));

        return $bytes;
    }

    /**
     * Handle the serialisation of "delegate registration" data.
     *
     * @param string $bytes
     *
     * @return string
     */
    private function handleSecondSignature(string $bytes): string
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
        $delegateBytes = $this->transaction->asset['delegate']['username'];
        dd($this->transaction->asset);
        $bytes .= pack('C', strlen($delegateBytes) / 2);
        $bytes .= hex2bin($delegateBytes);

        return $bytes;
    }

    /**
     * Handle the serialisation of "multi signature registration" data.
     *
     * @param string $bytes
     *
     * @return string
     */
    private function handleMultiSignature(string $bytes): string
    {
        $keysgroup = [];

        if (!isset($transaction->version) || 1 === $transaction->version) {
            foreach ($this->transaction->asset['multisignature']['keysgroup'] as $key) {
                $keysgroup[] = substr($key, 1);
            }
        } else {
            $keysgroup = $this->transaction->asset['multisignature']['keysgroup'];
        }

        $bytes .= pack('C', $this->transaction->asset['multisignature']['min']);
        $bytes .= pack('C', count($this->transaction->asset['multisignature']['keysgroup']));
        $bytes .= pack('C', $this->transaction->asset['multisignature']['lifetime']);
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

        $bytes .= pack('C', strlen($dag) / 2);
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
        $bytes .= pack('P', $this->transaction->amount);
        $bytes .= pack('h', $this->transaction->timelocktype);
        $bytes .= pack('V', $this->transaction->timelock);

        $recipientId = Base58::decodeCheck($this->transaction->recipientId)->getHex();
        $bytes .= pack('H'.strlen($recipientId), $recipientId);

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
        $bytes .= pack('V', count($this->transaction->asset['payments']));

        foreach ($this->transaction->asset['payments'] as $payment) {
            $bytes .= pack('P', $payment->amount);

            $recipientId = Base58::decodeCheck($payment->recipientId)->getHex();
            $bytes .= pack('H'.strlen($recipientId), $recipientId);
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
