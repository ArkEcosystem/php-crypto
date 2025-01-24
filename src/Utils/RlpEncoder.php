<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Utils;

use InvalidArgumentException;

class RlpEncoder
{
    public static function encode(mixed $object): string
    {
        $encoded = self::_encode($object);
        $hex     = '0x';
        $nibbles = '0123456789abcdef';
        foreach ($encoded as $byte) {
            $hex .= $nibbles[$byte >> 4];
            $hex .= $nibbles[$byte & 0x0f];
        }

        return $hex;
    }

    private static function _encode(mixed $object): array
    {
        if (is_array($object)) {
            $payload = [];
            foreach ($object as $child) {
                $payload = array_merge($payload, self::_encode($child));
            }
            if (count($payload) <= 55) {
                array_unshift($payload, 0xc0 + count($payload));

                return $payload;
            }
            $length = self::arrayifyInteger(count($payload));
            array_unshift($length, 0xf7 + count($length));

            return array_merge($length, $payload);
        }
        $data = self::getBytes($object);
        if (count($data) === 1 && $data[0] <= 0x7f) {
            return $data;
        } elseif (count($data) <= 55) {
            array_unshift($data, 0x80 + count($data));

            return $data;
        }
        $length = self::arrayifyInteger(count($data));
        array_unshift($length, 0xb7 + count($length));

        return array_merge($length, $data);
    }

    private static function arrayifyInteger(int $value): array
    {
        $result = [];
        while ($value > 0) {
            array_unshift($result, $value & 0xff);
            $value >>= 8;
        }

        return $result;
    }

    private static function getBytes(mixed $value): array
    {
        if (is_string($value)) {
            // Check if it matches a hex string with optional content after "0x"
            if (preg_match('/^0x([0-9a-fA-F]*)$/', $value, $match)) {
                $hex = $match[1]; // capture the part after "0x"

                // Empty "0x" means zero-length data
                if ($hex === '') {
                    return [];
                }

                // If there's an odd number of hex digits, left-pad with a '0'
                if (strlen($hex) % 2 !== 0) {
                    $hex = '0'.$hex;
                }

                $length = strlen($hex) / 2;
                $bytes  = [];
                for ($i = 0; $i < $length; $i++) {
                    $pair    = substr($hex, $i * 2, 2);
                    $bytes[] = hexdec($pair);
                }

                return $bytes;
            }

            // Fallback: treat the string as ASCII
            $bytes = [];
            for ($i = 0; $i < strlen($value); $i++) {
                $bytes[] = ord($value[$i]);
            }

            return $bytes;
        }

        if (is_int($value)) {
            if ($value === 0) {
                return [];
            }
            $result = [];
            while ($value > 0) {
                array_unshift($result, $value & 0xff);
                $value >>= 8;
            }

            return $result;
        }

        if (is_array($value)) {
            return array_map(fn ($v) => $v & 0xff, $value);
        }

        throw new InvalidArgumentException('invalid type');
    }
}
