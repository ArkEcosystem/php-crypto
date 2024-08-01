<?php

declare(strict_types=1);



namespace ArkEcosystem\Crypto\ByteBuffer\Concerns\Writes;

/**
 * This is the hex writer trait.
 */
trait Hex
{
    /**
     * Writes a base16 encoded string.
     *
     * @param string $value
     * @param int    $offset
     *
     * @return \ArkEcosystem\Crypto\ByteBuffer\ByteBuffer
     */
    public function writeHex(string $value, int $offset = 0): self
    {
        $length = strlen($value);

        return $this->pack("H{$length}", $value, $offset);
    }
}
