<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\ByteBuffer\Concerns\Writes;

use InvalidArgumentException;

/**
 * This is the unsigned integer writer trait.
 */
trait UnsignedInteger
{
    /**
     * Writes an 8bit unsigned integer.
     *
     * @param int $value
     * @param int $offset
     *
     * @return \ArkEcosystem\Crypto\ByteBuffer\ByteBuffer
     */
    public function writeUInt8(int $value, int $offset = 0): self
    {
        return $this->pack('C', $value, $offset);
    }

    /**
     * Writes an icbit unsigned integer.
     *
     * @param int $value
     * @param int $offset
     *
     * @return \ArkEcosystem\Crypto\ByteBuffer\ByteBuffer
     */
    public function writeUInt16(int $value, int $offset = 0): self
    {
        return $this->pack(['n', 'v', 'S'][$this->order], $value, $offset);
    }

    /**
     * Writes an 32bit unsigned integer.
     *
     * @param int $value
     * @param int $offset
     *
     * @return \ArkEcosystem\Crypto\ByteBuffer\ByteBuffer
     */
    public function writeUInt32(int $value, int $offset = 0): self
    {
        return $this->pack(['N', 'V', 'L'][$this->order], $value, $offset);
    }

    /**
     * Writes an 64bit unsigned integer.
     *
     * @param int $value
     * @param int $offset
     *
     * @return \ArkEcosystem\Crypto\ByteBuffer\ByteBuffer
     */
    public function writeUInt64(int $value, int $offset = 0): self
    {
        return $this->pack(['J', 'P', 'Q'][$this->order], $value, $offset);
    }

    /**
     * Writes a 256-bit unsigned integer (uint256).
     *
     * @param string|int|\GMP $value  The value to write as uint256.
     * @param int             $offset The offset at which to write the value.
     *
     * @throws InvalidArgumentException If the value does not fit into 256 bits.
     * @return \ArkEcosystem\Crypto\ByteBuffer\ByteBuffer
     */
    public function writeUInt256($value, int $offset = 0): self
    {
        // Convert the value to a GMP object for handling large numbers
        if (is_numeric($value) || is_string($value)) {
            $gmpValue = gmp_init($value);
        } elseif ($value instanceof \GMP) {
            $gmpValue = $value;
        } else {
            throw new InvalidArgumentException('The value must be a numeric string, integer, or GMP object.');
        }

        // Export the GMP number to a binary string (big-endian byte order)
        $binary = gmp_export($gmpValue);

        // Check if the binary length exceeds 32 bytes (256 bits)
        if (strlen($binary) > 32) {
            throw new InvalidArgumentException('The value must fit into 256 bits.');
        }

        // Pad the binary string to 32 bytes with zeros on the left
        $binary = str_pad($binary, 32, "\0", STR_PAD_LEFT);

        // Write the bytes into the buffer at the specified offset
        return $this->writeBytes($binary, $offset);
    }

    /**
     * Writes an 8bit unsigned integer. This is an alias of writeUInt8.
     *
     * @param int $value
     * @param int $offset
     *
     * @return \ArkEcosystem\Crypto\ByteBuffer\ByteBuffer
     */
    public function writeUByte(int $value, int $offset = 0): self
    {
        return $this->writeUInt8($value, $offset);
    }

    /**
     * Writes an 16bit unsigned integer. This is an alias of writeUInt16.
     *
     * @param int $value
     * @param int $offset
     *
     * @return \ArkEcosystem\Crypto\ByteBuffer\ByteBuffer
     */
    public function writeUShort(int $value, int $offset = 0): self
    {
        return $this->writeUInt16($value, $offset);
    }

    /**
     * Writes an 32bit unsigned integer. This is an alias of writeUInt32.
     *
     * @param int $value
     * @param int $offset
     *
     * @return \ArkEcosystem\Crypto\ByteBuffer\ByteBuffer
     */
    public function writeUInt(int $value, int $offset = 0): self
    {
        return $this->writeUInt32($value, $offset);
    }

    /**
     * Writes an 64bit unsigned integer. This is an alias of writeUInt64.
     *
     * @param int $value
     * @param int $offset
     *
     * @return \ArkEcosystem\Crypto\ByteBuffer\ByteBuffer
     */
    public function writeULong(int $value, int $offset = 0): self
    {
        return $this->writeUInt64($value, $offset);
    }
}
