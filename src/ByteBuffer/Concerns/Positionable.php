<?php

declare(strict_types=1);



namespace ArkEcosystem\Crypto\ByteBuffer\Concerns;

/**
 * This is the positionable trait.
 */
trait Positionable
{
    /**
     * Gets the absolute read/write offset.
     *
     * @return int
     */
    public function current(): int
    {
        return $this->offset;
    }

    /**
     * Sets this ByteBuffers absolute read/write offset.
     *
     * @param int $offset
     *
     * @return \ArkEcosystem\Crypto\ByteBuffer\ByteBuffer
     */
    public function position(int $offset): self
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * Skips N amount of bytes.
     *
     * @param int $length
     *
     * @return \ArkEcosystem\Crypto\ByteBuffer\ByteBuffer
     */
    public function skip(int $length): self
    {
        $this->offset += $length;

        return $this;
    }

    /**
     * Rewinds N amount of bytes.
     *
     * @param int $length
     *
     * @return \ArkEcosystem\Crypto\ByteBuffer\ByteBuffer
     */
    public function rewind(int $length): self
    {
        $this->offset -= $length;

        return $this;
    }

    /**
     * Resets this ByteBuffers offset.
     *
     * @return \ArkEcosystem\Crypto\ByteBuffer\ByteBuffer
     */
    public function reset(): self
    {
        $this->offset = 0;

        return $this;
    }

    /**
     * Clears this ByteBuffers offsets.
     *
     * @return \ArkEcosystem\Crypto\ByteBuffer\ByteBuffer
     */
    public function clear(): self
    {
        $this->offset = 0;
        $this->length = count($this->buffer);

        return $this;
    }
}
