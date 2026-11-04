<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Utils;

use ArkEcosystem\Crypto\Helpers;
use ArkEcosystem\Crypto\Identities\Address;
use ArkEcosystem\Crypto\Identities\PrivateKey;
use ArkEcosystem\Crypto\Identities\PublicKey;
use BitWasp\Bitcoin\Bitcoin;
use BitWasp\Bitcoin\Crypto\EcAdapter\EcAdapterFactory;
use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Signature\CompactSignature;
use BitWasp\Buffertools\Buffer;
use Exception;
use InvalidArgumentException;
use JsonException;
use kornrunner\Keccak;

class Message
{
    public const MESSAGE_PREFIX = "\x19Ethereum Signed Message:\n";

    /**
     * The message signer public key.
     */
    public string $publicKey;

    /**
     * The message signature.
     */
    public string $signature;

    /**
     * The message contents.
     */
    public string $message;

    /**
     * Create a new message instance.
     *
     * @param object $message
     */
    public function __construct(object $message)
    {
        $fields = [
            'publicKey' => 'public key',
            'signature' => 'signature',
            'message'   => 'message',
        ];

        foreach ($fields as $field => $label) {
            if (! property_exists($message, $field) || ! is_string($message->{$field})) {
                throw new InvalidArgumentException("The given message did not contain a valid {$label}.");
            }
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
            return new static((object) $message);
        }

        if (is_string($message)) {
            $decoded = json_decode($message);

            if (! is_object($decoded)) {
                throw new InvalidArgumentException('The given message was neither an object, array nor JSON.');
            }

            return new static($decoded);
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

        $signature = $privateKey->sign(self::hash($message));

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
        $signerAddress = static::recoverAddress($this->message, $this->signature);

        if ($signerAddress === null) {
            return false;
        }

        try {
            $address = Address::fromPublicKey(PublicKey::fromHex($this->publicKey)->publicKey);
        } catch (Exception) {
            return false;
        }

        return strcasecmp($signerAddress, $address) === 0;
    }

    /**
     * Recover the address that signed the given message, or null if the signature is malformed.
     *
     * @param string $message
     * @param string $signature
     *
     * @return string|null
     */
    public static function recoverAddress(string $message, string $signature): ?string
    {
        $compactSignature = self::parseSignature($signature);

        if ($compactSignature === null) {
            return null;
        }

        try {
            $publicKey = PublicKey::recover(self::hash($message), $compactSignature);
        } catch (Exception) {
            return null;
        }

        return Address::fromPublicKey($publicKey->publicKey);
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
     * @throws JsonException
     *
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR);
    }

    /**
     * @throws Exception
     */
    private static function hash(string $message): Buffer
    {
        return Buffer::hex(Keccak::hash(static::MESSAGE_PREFIX.strlen($message).$message, 256));
    }

    private static function parseSignature(string $signature): ?CompactSignature
    {
        $signature = Helpers::removeLeadingHexZero($signature);

        if (preg_match('/^[0-9a-f]{130}$/i', $signature) !== 1) {
            return null;
        }

        $recoveryId = match ((int) hexdec(substr($signature, 128, 2))) {
            0, 27   => 0,
            1, 28   => 1,
            default => null,
        };

        if ($recoveryId === null) {
            return null;
        }

        return new CompactSignature(
            adapter: EcAdapterFactory::getPhpEcc(Bitcoin::getMath(), Bitcoin::getGenerator()),
            r: gmp_init(substr($signature, 0, 64), 16),
            s: gmp_init(substr($signature, 64, 64), 16),
            recid: $recoveryId,
            compressed: true
        );
    }
}
