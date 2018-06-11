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

use ArkEcosystem\Crypto\Crypto;
use BitWasp\Bitcoin\Crypto\Hash;
use BitWasp\Buffertools\Buffer;
use stdClass;

/**
 * This is the abstract transaction class.
 *
 * @author Brian Faust <brian@ark.io>
 */
abstract class Transaction
{
    /**
     * Create a new transaction instance.
     */
    public function __construct()
    {
        $this->data              = new \stdClass();
        $this->data->recipientId = null;
        $this->data->type        = null;
        $this->data->amount      = null;
        $this->data->fee         = null;
        $this->data->vendorField = null;
        $this->data->timestamp   = $this->getTimeSinceEpoch();

        $this->data->senderPublicKey = null;

        $this->data->signature     = null;
        $this->data->signSignature = null;

        $this->data->id    = null;
        $this->data->asset = [];
    }

    /**
     * Convert the message to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJSON();
    }

    /**
     * Create a new transaction instance.
     *
     * @return \ArkEcosystem\Crypto\Transactions\Transaction
     */
    public static function create(): self
    {
        return new static();
    }

    /**
     * Set the transaction fee.
     *
     * @param int $fee
     *
     * @return \ArkEcosystem\Crypto\Transactions\Transaction
     */
    public function withFee(int $fee): self
    {
        $this->data->fee = $fee;

        return $this;
    }

    /**
     * Sign the transaction using the given secret.
     *
     * @param string $secret
     *
     * @return \ArkEcosystem\Crypto\Transactions\Transaction
     */
    public function sign(string $secret): Transaction
    {
        $keys                          = Crypto::getKeys($secret);
        $this->data->senderPublicKey   = $keys->getPublicKey()->getHex();

        Crypto::sign($this->getStruct(), $keys);

        return $this;
    }

    /**
     * Sign the transaction using the given second secret.
     *
     * @param string $secondSecret
     *
     * @return \ArkEcosystem\Crypto\Transactions\Transaction
     */
    public function secondSign(string $secondSecret): Transaction
    {
        Crypto::secondSign($this->getStruct(), Crypto::getKeys($secondSecret));

        return $this;
    }

    /**
     * Verify the transaction validity.
     *
     * @return bool
     */
    public function verify(): bool
    {
        return Crypto::verify($this->getStruct());
    }

    /**
     * Verify the transaction validity with a second signature.
     *
     * @return bool
     */
    public function secondVerify(string $secondSecret): bool
    {
        return Crypto::secondVerify(
            $this->getStruct(),
            Crypto::getKeys($secondSecret)->getPublicKey()->getHex()
        );
    }

    /**
     * Convert the message to its plain object representation.
     *
     * @return \stdClass
     */
    public function getStruct(): stdClass
    {
        $idBytes        = Crypto::getBytes($this->data, false, false);
        $this->data->id = Hash::sha256(new Buffer($idBytes))->getHex();

        if (empty($this->data->signSignature)) {
            unset($this->data->signSignature);
        }

        if (empty($this->data->asset)) {
            unset($this->data->asset);
        }

        return $this->data;
    }

    /**
     * Convert the message to its JSON representation.
     *
     * @return string
     */
    public function toJSON(): string
    {
        return json_encode($this->data);
    }

    /**
     * Get the transaction timestamp.
     *
     * @return int
     */
    protected function getTimeSinceEpoch(): int
    {
        return time() - strtotime('2017-03-21 13:00:00');
    }
}
