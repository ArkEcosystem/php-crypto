<?php

declare(strict_types=1);



namespace ArkEcosystem\Crypto\Binary\Buffer\Writer\Concerns;

use ArkEcosystem\Crypto\Binary\UnsignedInteger\Writer;

trait UnsignedInteger
{
    /**
     * Write an unsigned 8 bit integer.
     *
     * @param int $value
     *
     * @return \ArkEcosystem\Crypto\Binary\Buffer\Writer\Buffer
     */
    public function writeUInt8(int $value): self
    {
        $this->bytes .= Writer::bit8($value);

        return $this;
    }

    /**
     * Write an unsigned 16 bit integer.
     *
     * @param int $value
     *
     * @return \ArkEcosystem\Crypto\Binary\Buffer\Writer\Buffer
     */
    public function writeUInt16(int $value): self
    {
        $this->bytes .= Writer::bit16($value);

        return $this;
    }

    /**
     * Write an unsigned 32 bit integer.
     *
     * @param int $value
     *
     * @return \ArkEcosystem\Crypto\Binary\Buffer\Writer\Buffer
     */
    public function writeUInt32(int $value): self
    {
        $this->bytes .= Writer::bit32($value);

        return $this;
    }

    /**
     * Write an unsigned 64 bit integer.
     *
     * @param int $value
     *
     * @return \ArkEcosystem\Crypto\Binary\Buffer\Writer\Buffer
     */
    public function writeUInt64(int $value): self
    {
        $this->bytes .= Writer::bit64($value);

        return $this;
    }
}
