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

namespace ArkEcosystem\Crypto\ByteBuffer\Concerns;

use InvalidArgumentException;

/**
 * This is the initialisable trait.
 */
trait Initialisable
{
    /**
     * Creates a ByteBuffer from a binary string.
     *
     * @param string $value
     *
     * @return \ArkEcosystem\Crypto\ByteBuffer\ByteBuffer
     */
    public static function fromBinary(string $value): self
    {
        return new static($value);
    }

    /**
     * Creates a ByteBuffer from a hex string.
     *
     * @param string $value
     *
     * @return \ArkEcosystem\Crypto\ByteBuffer\ByteBuffer
     */
    public static function fromHex(string $value): self
    {
        if (strlen($value) > 0 && !ctype_xdigit($value)) {
            throw new InvalidArgumentException('Buffer::hex: non-hex character passed');
        }

        return new static(pack('H*', $value));
    }

    /**
     * Creates a ByteBuffer from a UTF-8 string.
     *
     * @param string $value
     *
     * @return \ArkEcosystem\Crypto\ByteBuffer\ByteBuffer
     */
    public static function fromUTF8(string $value): self
    {
        return new static(mb_convert_encoding($value, 'UTF-8', 'UTF-8'));
    }

    /**
     * Creates a ByteBuffer from a base64 string.
     *
     * @param string $value
     *
     * @return \ArkEcosystem\Crypto\ByteBuffer\ByteBuffer
     */
    public static function fromBase64(string $value): self
    {
        return new static(base64_decode($value, true));
    }

    /**
     * Creates a ByteBuffer from an array.
     *
     * @param array $value
     *
     * @return \ArkEcosystem\Crypto\ByteBuffer\ByteBuffer
     */
    public static function fromArray(array $value): self
    {
        return new static($value);
    }

    /**
     * Get the buffer as a plain string.
     *
     * @param string $value
     * @param string $encoding
     *
     * @return \ArkEcosystem\Crypto\ByteBuffer\ByteBuffer
     */
    public static function fromString(string $value, string $encoding): self
    {
        switch ($encoding) {
            case 'binary':
                return static::fromBinary($value, $encoding);
            case 'hex':
                return static::fromHex($value, $encoding);
            case 'utf8':
                return static::fromUTF8($value, $encoding);
            case 'base64':
                return static::fromBase64($value, $encoding);
            default:
                throw new InvalidArgumentException("The encoding [{$encoding}] is not supported.");
        }
    }
}
