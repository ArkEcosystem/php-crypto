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

namespace ArkEcosystem\Crypto\ByteBuffer\Concerns\Reads;

/**
 * This is the integer reader trait.
 */
trait Integer
{
    /**
     * Reads an 8bit signed integer.
     *
     * @param int $offset
     *
     * @return int
     */
    public function readInt8(int $offset = 0): int
    {
        return $this->unpack('c', $offset);
    }

    /**
     * Reads an 16bit signed integer.
     *
     * @param int $offset
     *
     * @return int
     */
    public function readInt16(int $offset = 0): int
    {
        return $this->unpack('s', $offset);
    }

    /**
     * Reads an 32bit signed integer.
     *
     * @param int $offset
     *
     * @return int
     */
    public function readInt32(int $offset = 0): int
    {
        return $this->unpack('l', $offset);
    }

    /**
     * Reads an 64bit signed integer.
     *
     * @param int $offset
     *
     * @return int
     */
    public function readInt64(int $offset = 0): int
    {
        return $this->unpack('q', $offset);
    }

    /**
     * Reads an 8bit signed integer. This is an alias of readInt8.
     *
     * @param int $offset
     *
     * @return int
     */
    public function readByte(int $offset = 0): int
    {
        return $this->readInt8($offset);
    }

    /**
     * Reads an 16bit signed integer. This is an alias of readInt16.
     *
     * @param int $offset
     *
     * @return int
     */
    public function readShort(int $offset = 0): int
    {
        return $this->readInt16($offset);
    }

    /**
     * Reads an 32bit signed integer. This is an alias of readInt32.
     *
     * @param int $offset
     *
     * @return int
     */
    public function readInt(int $offset = 0): int
    {
        return $this->readInt32($offset);
    }

    /**
     * Reads an 64bit signed integer. This is an alias of readInt64.
     *
     * @param int $offset
     *
     * @return int
     */
    public function readLong(int $offset = 0): int
    {
        return $this->readInt64($offset);
    }
}
