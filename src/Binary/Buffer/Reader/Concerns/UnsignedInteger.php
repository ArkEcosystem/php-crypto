<?php

declare(strict_types=1);



namespace ArkEcosystem\Crypto\Binary\Buffer\Reader\Concerns;

use ArkEcosystem\Crypto\Binary\UnsignedInteger\Reader;

trait UnsignedInteger
{
    /**
     * Read an unsigned 8 bit integer.
     *
     * @return \ArkEcosystem\Crypto\Binary\Buffer\Reader\Buffer
     */
    public function readUInt8()
    {
        $value = Reader::bit8($this->bytes);

        $this->skip(1);

        return $value;
    }

    /**
     * Read an unsigned 16 bit integer.
     *
     * @return \ArkEcosystem\Crypto\Binary\Buffer\Reader\Buffer
     */
    public function readUInt16()
    {
        $value = Reader::bit16($this->bytes);

        $this->skip(2);

        return $value;
    }

    /**
     * Read an unsigned 32 bit integer.
     *
     * @return \ArkEcosystem\Crypto\Binary\Buffer\Reader\Buffer
     */
    public function readUInt32()
    {
        $value = Reader::bit32($this->bytes);

        $this->skip(4);

        return $value;
    }

    /**
     * Read an unsigned 64 bit integer.
     *
     * @return \ArkEcosystem\Crypto\Binary\Buffer\Reader\Buffer
     */
    public function readUInt64()
    {
        $value = Reader::bit64($this->bytes);

        $this->skip(8);

        return $value;
    }
}
