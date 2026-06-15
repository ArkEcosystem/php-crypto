<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\ByteBuffer\Concerns\Reads;

/**
 * This is the unsigned integer reader trait.
 */
trait UnsignedInteger
{
    /**
     * Reads an 8bit unsigned integer.
     *
     * @param int $offset
     *
     * @return int
     */
    public function readUInt8(int $offset = 0): int
    {
        return $this->unpack('C', $offset);
    }

    /**
     * Reads an 16bit unsigned integer.
     *
     * @param int $offset
     *
     * @return int
     */
    public function readUInt16(int $offset = 0): int
    {
        return $this->unpack(['n', 'v', 'S'][$this->order], $offset);
    }

    /**
     * Reads an 32bit unsigned integer.
     *
     * @param int $offset
     *
     * @return int
     */
    public function readUInt32(int $offset = 0): int
    {
        return $this->unpack(['N', 'V', 'L'][$this->order], $offset);
    }

    /**
     * Reads an 64bit unsigned integer.
     *
     * @param int $offset
     *
     * @return int
     */
    public function readUInt64(int $offset = 0): int
    {
        return $this->unpack(['J', 'P', 'Q'][$this->order], $offset);
    }

    /**
     * Reads a 256-bit unsigned integer (uint256) and returns it as a string.
     *
     * @param int $offset The offset from which to start reading the value.
     *
     * @return string The 256-bit unsigned integer as a string.
     */
    public function readUInt256(int $offset = 0): string
    {
        // Ensure we skip to the correct offset
        $this->skip($offset);

        // Collect the 32 bytes (256 bits)
        $bytes = [];
        for ($i = 0; $i < 32; $i++) {
        // Read one byte from the buffer
            $byte    = ord($this->buffer[$this->offset++]); // Move offset after reading
            $bytes[] = $byte;
        }

        // Reverse the byte order for little-endian interpretation
        $bytes = array_reverse($bytes);

        // Convert bytes to GMP (big-endian after reversing)
        $gmpValue = gmp_init(0);
        for ($i = 0; $i < 32; $i++) {
            $gmpValue = gmp_add($gmpValue, gmp_mul(gmp_init($bytes[$i]), gmp_pow(2, 8 * $i)));
        }

        // Return the GMP value as a string
        return gmp_strval($gmpValue);
    }

    /**
     * Reads a 8bit unsigned integer. This is an alias of readUInt8.
     *
     * @param int $offset
     *
     * @return int
     */
    public function readUByte(int $offset = 0): int
    {
        return $this->readUInt8($offset);
    }

    /**
     * Reads a 16bit unsigned integer. This is an alias of readUInt16.
     *
     * @param int $offset
     *
     * @return int
     */
    public function readUShort(int $offset = 0): int
    {
        return $this->readUInt16($offset);
    }

    /**
     * Reads a 32bit unsigned integer. This is an alias of readUInt32.
     *
     * @param int $offset
     *
     * @return int
     */
    public function readUInt(int $offset = 0): int
    {
        return $this->readUInt32($offset);
    }

    /**
     * Reads a 64bit unsigned integer. This is an alias of readUInt64.
     *
     * @param int $offset
     *
     * @return int
     */
    public function readULong(int $offset = 0): int
    {
        return $this->readUInt64($offset);
    }
}
