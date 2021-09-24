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

namespace ArkEcosystem\Crypto\Binary\Buffer\Writer;

use ArkEcosystem\Crypto\Binary\Hex\Writer as Hex;

class Buffer
{
    use Concerns\Generic;
    use Concerns\Hex;
    use Concerns\Integer;
    use Concerns\UnsignedInteger;

    /**
     * The concatenated bytes.
     *
     * @var string
     */
    private $bytes;

    /**
     * Create a new byte buffer instance.
     */
    public function __construct()
    {
        $this->bytes = '';
    }

    /**
     * Return the raw bytes representation.
     *
     * @return string
     */
    public function toBytes(): string
    {
        return $this->bytes;
    }

    /**
     * Return the hex representation of the bytes.
     *
     * @return string
     */
    public function toHex(): string
    {
        return bin2hex($this->bytes);
    }
}
