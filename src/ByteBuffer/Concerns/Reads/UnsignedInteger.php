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
