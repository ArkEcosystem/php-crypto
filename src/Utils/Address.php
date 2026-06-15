<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Utils;

use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;
use BitWasp\Buffertools\Buffer;
use kornrunner\Keccak;

class Address
{
    private static array $cache = [];

    /**
     * Validate the given address.
     *
     * @param string $address
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
        if (isset(self::$cache[$address])) {
            return self::$cache[$address];
        }

        $rawAddress      = strtolower(substr($address, 2));
        $hash            = Keccak::hash($rawAddress, 256);
        $checksumAddress = '0x';

        for ($i = 0; $i < 40; $i++) {
            if (intval($hash[$i], 16) >= 8) {
                $checksumAddress .= strtoupper($rawAddress[$i]);
            } else {
                $checksumAddress .= $rawAddress[$i];
            }
        }

        self::$cache[$address] = $checksumAddress;

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

        return $address;

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
