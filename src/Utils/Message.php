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

namespace ArkEcosystem\Crypto\Utils;

use ArkEcosystem\Crypto\Identities\PrivateKey;
use BitWasp\Bitcoin\Crypto\Hash;
use BitWasp\Bitcoin\Key\Factory\PublicKeyFactory;
use BitWasp\Bitcoin\Signature\SignatureFactory;
use BitWasp\Buffertools\Buffer;
use InvalidArgumentException;

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
     * @param object $message
     */
    public function __construct(object $message)
    {
        if (property_exists($message, 'publickey')) {
            $this->publicKey = $message->publickey;
        } elseif (property_exists($message, 'publicKey')) {
            $this->publicKey = $message->publicKey;
        } elseif (property_exists($message, 'signatory')) {
            $this->publicKey = $message->signatory;
        } else {
            throw new InvalidArgumentException('The given message did not contain a valid public key.');
        }

        $this->signature = $message->signature;
        $this->message   = $message->message;
    }

    /**
     * Convert the message to its JSON representation.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->toJson();
    }

    /**
     * Create a new message instance.
     *
     * @param mixed $message
     *
     * @return \ArkEcosystem\Crypto\Utils\Message
     */
    public static function new($message): self
    {
        if (is_object($message)) {
            return new static($message);
        }

        if (is_array($message)) {
            return new static(json_decode(json_encode($message)));
        }

        if (is_string($message)) {
            return new static(json_decode($message));
        }

        throw new InvalidArgumentException('The given message was neither an object, array nor JSON.');
    }

    /**
     * Sign a message using the given passphrase.
     *
     * @param string $message
     * @param string $passphrase
     *
     * @return \ArkEcosystem\Crypto\Utils\Message
     */
    public static function sign(string $message, string $passphrase): self
    {
        $keys = PrivateKey::fromPassphrase($passphrase);

        return static::new([
            'publickey' => $keys->getPublicKey()->getHex(),
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
        $factory = new PublicKeyFactory();

        return $factory->fromHex($this->publicKey)->verify(
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
            'publickey' => $this->publicKey,
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
