<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\ByteBuffer\Concerns\Reads;

/**
 * This is the hex reader trait.
 */
trait Hex
{
    /**
     * Reads a base16 encoded string.
     *
     * @param int $length
     *
     * @return string
     */
    public function readHex(int $length): string
    {
        return $this->unpack("H{$length}");
    }

    /**
     * Reads a base16 encoded string and decode it to binary.
     *
     * @param int $length
     *
     * @return string
     */
    public function readHexString(int $length): string
    {
        return pack('H*', $this->readHex($length));
    }
}
