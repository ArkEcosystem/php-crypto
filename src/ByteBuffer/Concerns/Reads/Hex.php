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
