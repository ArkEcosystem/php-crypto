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

use BitWasp\Bitcoin\Crypto\Hash;
use BitWasp\Bitcoin\Key\PublicKeyFactory;
use BitWasp\Bitcoin\Signature\SignatureFactory;
use BitWasp\Buffertools\Buffer;

/**
 * This is the message class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class Message
{
    /**
     * The message signer public key.
     *
     * @var string
     */
    public $publicKey;

    /**
     * The message signature.
     *
     * @var string
     */
    public $signature;

    /**
     * The message contents.
     *
     * @var string
     */
    public $message;

    /**
     * Create a new message instance.
     *
     * @param array $message
     */
    public function __construct(array $message)
    {
        $this->publicKey = $message['publicKey'];
        $this->signature = $message['signature'];
        $this->message   = $message['message'];
    }

    /**
     * Convert the message to its JSON representation.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Create a new message instance from an array.
     *
     * @param array $message
     *
     * @return \ArkEcosystem\Crypto\Message
     */
    public static function fromArray(array $message): self
    {
        return new static($message);
    }

    /**
     * Create a new message instance from a JSON string.
     *
     * @param string $message
     *
     * @return \ArkEcosystem\Crypto\Message
     */
    public static function fromJSON(string $message): self
    {
        return new static(json_decode($message, true));
    }

    /**
     * Sign a message using the given secret.
     *
     * @param string $message
     * @param string $secret
     *
     * @return \ArkEcosystem\Crypto\Message
     */
    public static function sign(string $message, string $secret): self
    {
        $keys = Crypto::getKeys($secret);

        return new static([
            'publicKey' => $keys->getPublicKey()->getHex(),
            'signature' => $keys->sign(Hash::sha256(new Buffer($message)))->getBuffer()->getHex(),
            'message'   => $message,
        ]);
    }

    /**
     * Verify the message contents.
     *
     * @return bool
     */
    public function verify(): bool
    {
        return PublicKeyFactory::fromHex($this->publicKey)->verify(
            new Buffer(hash('sha256', $this->message, true)),
            SignatureFactory::fromHex($this->signature)
        );
    }

    /**
     * Convert the message to its array representation.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'publicKey' => $this->publicKey,
            'signature' => $this->signature,
            'message'   => $this->message,
        ];
    }

    /**
     * Convert the message to its JSON representation.
     *
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }
}
