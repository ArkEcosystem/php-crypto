<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\ByteBuffer\Concerns\Reads;

/**
 * This is the floats reader trait.
 */
trait Floats
{
    /**
     * Reads a 32bit float.
     *
     * @param int $offset
     *
     * @return float
     */
    public function readFloat32(int $offset = 0): float
    {
        return $this->unpack(['G', 'g', 'f'][$this->order], $offset);
    }

    /**
     * Reads a 64bit float.
     *
     * @param int $offset
     *
     * @return float
     */
    public function readFloat64(int $offset = 0): float
    {
        return $this->unpack(['E', 'e', 'd'][$this->order], $offset);
    }

    /**
     * Reads a 64bit float. This is an alias of readFloat64.
     *
     * @param int $offset
     *
     * @return float
     */
    public function readDouble(int $offset = 0): float
    {
        return $this->readFloat64($offset);
    }
}
