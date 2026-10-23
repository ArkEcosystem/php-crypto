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

namespace ArkEcosystem\Crypto\Binary;

use InvalidArgumentException;

/**
 * An immutable sequence of raw bytes.
 */
class Bytes
{
    /**
     * @var string
     */
    private $binary;

    /**
     * @param string $binary
     */
    public function __construct(string $binary = '')
    {
        $this->binary = $binary;
    }

    /**
     * Create an instance from a hex string.
     *
     * @param string $hex
     *
     * @return self
     */
    public static function fromHex(string $hex): self
    {
        if (strlen($hex) % 2 !== 0 || ($hex !== '' && ! ctype_xdigit($hex))) {
            throw new InvalidArgumentException('The given value is not a valid hex string.');
        }

        return new self((string) hex2bin($hex));
    }

    /**
     * Get the bytes as a binary string.
     *
     * @return string
     */
    public function getBinary(): string
    {
        return $this->binary;
    }

    /**
     * Get the bytes as a lowercase hex string.
     *
     * @return string
     */
    public function getHex(): string
    {
        return bin2hex($this->binary);
    }

    /**
     * Get the number of bytes.
     *
     * @return int
     */
    public function getSize(): int
    {
        return strlen($this->binary);
    }
}
