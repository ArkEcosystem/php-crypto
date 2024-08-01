<?php

declare(strict_types=1);



namespace ArkEcosystem\Crypto\Binary\Buffer\Writer\Concerns;

use ArkEcosystem\Crypto\Binary\Hex\Writer;

trait Hex
{
    /**
     * Write the given hex value as binary with a high nibble.
     *
     * @param string $value
     *
     * @return \ArkEcosystem\Crypto\Binary\Buffer\Writer\Buffer
     */
    public function writeHex(string $value): self
    {
        $this->bytes .= Writer::high($value, strlen($value));

        return $this;
    }

    /**
     * Write the given hex value as binary.
     *
     * @param string $value
     *
     * @return \ArkEcosystem\Crypto\Binary\Buffer\Writer\Buffer
     */
    public function writeHexBytes(string $value): self
    {
        $this->bytes .= hex2bin($value);

        return $this;
    }
}
