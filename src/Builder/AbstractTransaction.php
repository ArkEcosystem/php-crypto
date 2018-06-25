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

namespace ArkEcosystem\Crypto\Builder;

use ArkEcosystem\Crypto\Configuration\Fee;
use ArkEcosystem\Crypto\Crypto;
use ArkEcosystem\Crypto\Identity\PrivateKey;
use ArkEcosystem\Crypto\Identity\PublicKey;
use BitWasp\Bitcoin\Crypto\Hash;
use BitWasp\Buffertools\Buffer;
use stdClass;
use function Stringy\create as s;

/**
 * This is the abstract transaction class.
 *
 * @author Brian Faust <brian@ark.io>
 */
abstract class AbstractTransaction
{
    /**
     * Create a new transaction instance.
     */
    public function __construct()
    {
        $this->data              = new \stdClass();
        $this->data->recipientId = null;
        $this->data->type        = $this->getType();
        $this->data->amount      = 0;
        $this->data->fee         = $this->getFee();
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
     * @return \ArkEcosystem\Crypto\Builder\AbstractTransaction
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
     * @return \ArkEcosystem\Crypto\Builder\AbstractTransaction
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
     * @return \ArkEcosystem\Crypto\Builder\AbstractTransaction
     */
    public function sign(string $secret): AbstractTransaction
    {
        $keys                          = PrivateKey::fromSecret($secret);
        $this->data->senderPublicKey   = $keys->getPublicKey()->getHex();

        Crypto::sign($this->getSignedObject(), $keys);

        return $this;
    }

    /**
     * Sign the transaction using the given second secret.
     *
     * @param string $secondSecret
     *
     * @return \ArkEcosystem\Crypto\Builder\AbstractTransaction
     */
    public function secondSign(string $secondSecret): AbstractTransaction
    {
        Crypto::secondSign($this->getSignedObject(), PrivateKey::fromSecret($secondSecret));

        return $this;
    }

    /**
     * Verify the transaction validity.
     *
     * @return bool
     */
    public function verify(): bool
    {
        return Crypto::verify($this->getSignedObject());
    }

    /**
     * Verify the transaction validity with a second signature.
     *
     * @return bool
     */
    public function secondVerify(string $secondSecret): bool
    {
        return Crypto::secondVerify(
            $this->getSignedObject(),
            PublicKey::fromSecret($secondSecret)->getHex()
        );
    }

    /**
     * Convert the message to its plain object representation.
     *
     * @return \stdClass
     */
    public function getSignedObject(): stdClass
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

    /**
     * Get the transaction type.
     *
     * @return int
     */
    private function getType(): int
    {
        $identifier = $this->getIdentifier();

        return constant("ArkEcosystem\Crypto\Enums\Types::{$identifier}");
    }

    /**
     * Get the transaction fee.
     *
     * @return int
     */
    private function getFee(): int
    {
        return Fee::get($this->data->type);
    }

    /**
     * Get the class identifier to be used with enums.
     *
     * @return string
     */
    private function getIdentifier(): string
    {
        $className = (new \ReflectionClass($this))->getShortName();

        return (string) s($className)
            ->underscored()
            ->toUpperCase();
    }
}
