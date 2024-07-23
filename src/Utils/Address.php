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

namespace ArkEcosystem\Crypto\Utils;

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;
use ArkEcosystem\Crypto\Networks\AbstractNetwork;
use BitWasp\Buffertools\Buffer;
use kornrunner\Keccak;

class Address
{
    /**
     * Validate the given address.
     *
     * @param string $address
     * @param AbstractNetwork|int|null $network
     *
     * @return bool
     */
    public static function validate(string $address): bool
    {
        // Simple validation to check if the address starts with 0x and is 42 characters long
        return preg_match('/^0x[a-fA-F0-9]{40}$/', $address) === 1;
    }

    /**
     * Convert to checksum address.
     *
     * @param string $address
     *
     * @return string
     */
    public static function toChecksumAddress(string $address): string
    {
        $address         = strtolower(substr($address, 2));
        $hash            = Keccak::hash($address, 256);
        $checksumAddress = '0x';

        for ($i = 0; $i < 40; $i++) {
            if (intval($hash[$i], 16) >= 8) {
                $checksumAddress .= strtoupper($address[$i]);
            } else {
                $checksumAddress .= $address[$i];
            }
        }

        return $checksumAddress;
    }

    /**
     * Convert to hex string without 0x prefix.
     *
     * @param string $address
     *
     * @return string
     */
    public static function toBufferHexString(string $address): string
    {
        if (strpos($address, '0x') === 0) {
            $address = substr($address, 2);
        }

        return strtolower($address);
    }

    /**
     * Extract the address from a byte buffer.
     *
     * @param ByteBuffer $buffer
     *
     * @return string
     */
    public static function fromByteBuffer(ByteBuffer $buffer): string
    {
        $hexAddress = '0x'.(new Buffer(hex2bin($buffer->readHex(20 * 2))))->getHex();

        return self::toChecksumAddress($hexAddress);
    }
}
