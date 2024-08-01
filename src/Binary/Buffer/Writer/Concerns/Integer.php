<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Binary\Buffer\Writer\Concerns;

use ArkEcosystem\Crypto\Binary\Integer\Writer;

trait Integer
{
    /**
     * Write a signed 8 bit integer.
     *
     * @param int $value
     *
     * @return \ArkEcosystem\Crypto\Binary\Buffer\Writer\Buffer
     */
    public function writeInt8(int $value): self
    {
        $this->bytes .= Writer::bit8($value);

        return $this;
    }

    /**
     * Write a signed 16 bit integer.
     *
     * @param int $value
     *
     * @return \ArkEcosystem\Crypto\Binary\Buffer\Writer\Buffer
     */
    public function writeInt16(int $value): self
    {
        $this->bytes .= Writer::bit16($value);

        return $this;
    }

    /**
     * Write a signed 32 bit integer.
     *
     * @param int $value
     *
     * @return \ArkEcosystem\Crypto\Binary\Buffer\Writer\Buffer
     */
    public function writeInt32(int $value): self
    {
        $this->bytes .= Writer::bit32($value);

        return $this;
    }

    /**
     * Write a signed 64 bit integer.
     *
     * @param int $value
     *
     * @return \ArkEcosystem\Crypto\Binary\Buffer\Writer\Buffer
     */
    public function writeInt64(int $value): self
    {
        $this->bytes .= Writer::bit64($value);

        return $this;
    }
}
