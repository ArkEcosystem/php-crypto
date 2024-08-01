<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\ByteBuffer\Concerns\Writes;

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
