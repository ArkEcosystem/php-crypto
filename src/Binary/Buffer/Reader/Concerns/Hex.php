<?php

declare(strict_types=1);



namespace ArkEcosystem\Crypto\Binary\Buffer\Reader\Concerns;

use ArkEcosystem\Crypto\Binary\Hex\Reader;

trait Hex
{
    /**
     * Read N characters of bytes in hex with high nibble.
     *
     * @param int $length
     *
     * @return \ArkEcosystem\Crypto\Binary\Buffer\Reader\Buffer
     */
    public function readHex(int $length)
    {
        $value = Reader::high($this->bytes, 0, $length * 2);

        $this->skip($length);

        return $value;
    }

    /**
     * Read N characters of a raw hex string and turn them into bytes.
     *
     * @param int $length
     *
     * @return \ArkEcosystem\Crypto\Binary\Buffer\Reader\Buffer
     */
    public function readHexBytes(int $length)
    {
        return hex2bin($this->readHexRaw($length));
    }

    /**
     * Read N characters of a raw hex string.
     *
     * @param int $length
     *
     * @return \ArkEcosystem\Crypto\Binary\Buffer\Reader\Buffer
     */
    public function readHexRaw(int $length)
    {
        return substr($this->hex, $this->offset, $length);
    }
}
