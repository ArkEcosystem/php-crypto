<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Utils;

use ArkEcosystem\Crypto\Helpers;
use ArkEcosystem\Crypto\Identities\PrivateKey;
use BitWasp\Bitcoin\Bitcoin;
use BitWasp\Bitcoin\Crypto\EcAdapter\EcAdapterFactory;
use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Signature\CompactSignature;
use BitWasp\Bitcoin\Key\Factory\PublicKeyFactory;
use BitWasp\Buffertools\Buffer;
use InvalidArgumentException;
use kornrunner\Keccak;

class Message
{
    const MESSAGE_PREFIX = "\x19Ethereum Signed Message:\n";

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
        if (! property_exists($message, 'publicKey')) {
            throw new InvalidArgumentException('The given message did not contain a valid public key.');
        }

        $this->publicKey = $message->publicKey;
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
     * @return Message
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
     * @return Message
     */
    public static function sign(string $message, string $passphrase): self
    {
        $privateKey = PrivateKey::fromPassphrase($passphrase);

        $hash = Keccak::hash(static::MESSAGE_PREFIX.strlen($message).$message, 256);

        $signature = $privateKey->sign(Buffer::hex($hash));

        $r = Helpers::gmpToHex($signature->getR());
        $s = Helpers::gmpToHex($signature->getS());
        $v = str_pad(dechex($signature->getRecoveryId() + 27), 2, '0', STR_PAD_LEFT);

        return static::new([
            'publicKey' => $privateKey->publicKey,
            'signature' => $r.$s.$v,
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

        $signature = $this->getSignature();

        $message = static::MESSAGE_PREFIX.strlen($this->message).$this->message;

        return $factory
            ->fromHex($this->publicKey)
            ->verify(
                Buffer::hex(Keccak::hash($message, 256)),
                $signature,
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

    private function getSignature(): CompactSignature
    {
        $r = substr($this->signature, 0, 64);
        $s = substr($this->signature, 64, 64);
        $v = hexdec(substr($this->signature, 128, 2));

        $ecAdapter = EcAdapterFactory::getPhpEcc(
            Bitcoin::getMath(),
            Bitcoin::getGenerator()
        );

        $recoverId = $v - 27;
        $r         = gmp_init($r, 16);
        $s         = gmp_init($s, 16);

        return new CompactSignature(
            adapter: $ecAdapter,
            r: $r,
            s: $s,
            recid: $recoverId,
            compressed: true
        );
    }
}
