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

namespace ArkEcosystem\Crypto\Binary\Buffer\Reader;

class Buffer
{
    use Concerns\Hex;
    use Concerns\Integer;
    use Concerns\UnsignedInteger;

    /**
     * The hex representation.
     *
     * @var string
     */
    private $hex;

    /**
     * The concatenated bytes.
     *
     * @var string
     */
    private $bytes;

    /**
     * The byte offset at which to start reading.
     *
     * @var string
     */
    private $offset;

    /**
     * Create a new byte buffer instance.
     */
    public function __construct(string $value)
    {
        $this->hex   = $value;
        $this->bytes = hex2bin($value);
    }

    /**
     * Return the raw bytes representation.
     *
     * @return string
     */
    public static function fromHex(string $value): self
    {
        return new static($value);
    }

    /**
     * Set the cursor to N.
     *
     * @param int $value
     *
     * @return Buffer
     */
    public function position(int $value): self
    {
        $this->offset = $value;
        $this->bytes  = substr(hex2bin($this->hex), $value);

        return $this;
    }

    /**
     * Move the cursor by N amount of bytes.
     *
     * @param int $value
     *
     * @return Buffer
     */
    public function skip(int $value): self
    {
        $this->offset += $value;
        $this->bytes = substr($this->bytes, $value);

        return $this;
    }

    /**
     * Get the binary representation of the buffer.
     *
     * @return string
     */
    public function toBinary(): string
    {
        return $this->bytes;
    }

    /**
     * Get the hex representation of the buffer.
     *
     * @return string
     */
    public function toHex(): string
    {
        return $this->hex;
    }
}
