<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Utils;

use InvalidArgumentException;

class RlpEncoder
{
    public static function decode(string $data): mixed
    {
        $bytes = self::getBytes($data, 'data');
        $decoded = self::_decode($bytes, 0);

        if ($decoded['consumed'] !== count($bytes)) {
            throw new InvalidArgumentException('unexpected junk after RLP payload');
        }

        return $decoded['result'];
    }
    
    private static function getBytes(string $value, string $name = 'value'): array
    {
        if (preg_match('/^0x(?:[0-9a-fA-F]{2})*$/', $value)) {
            $hex = substr($value, 2);
            $length = strlen($hex) / 2;
            $bytes = [];

            for ($i = 0; $i < $length; $i++) {
                $pair = substr($hex, $i * 2, 2);
                $bytes[] = hexdec($pair);
            }
            return $bytes;
        }

        throw new InvalidArgumentException(
            sprintf('Invalid BytesLike value for "%s": %s', $name, $value)
        );
    }

    private static function hexlify(array $data): string
    {
        $hex = '';
        foreach ($data as $byte) {
            $hex .= sprintf('%02x', $byte);
        }
        return '0x' . $hex;
    }

    private static function hexlifyByte(int $value): string
    {
        return '0x' . sprintf('%02x', $value & 0xff);
    }

    private static function unarrayifyInteger(array $data, int $offset, int $length): int
    {
        $result = 0;
        for ($i = 0; $i < $length; $i++) {
            $result = ($result << 8) + $data[$offset + $i];
        }
        return $result;
    }

    /**
     * Decodes a list of consecutive RLP items within $length.
     */
    private static function _decodeChildren(array $data, int $offset, int $childOffset, int $length): array
    {
        $result = [];
        $end = $offset + 1 + $length;

        while ($childOffset < $end) {
            $decoded = self::_decode($data, $childOffset);
            $result[] = $decoded['result'];
            $childOffset += $decoded['consumed'];

            if ($childOffset > $end) {
                throw new InvalidArgumentException('child data too short or malformed');
            }
        }

        return [
            'consumed' => 1 + $length,
            'result'   => $result
        ];
    }

    private static function _decode(array $data, int $offset): array
    {
        self::checkOffset($offset, $data);

        $prefix = $data[$offset];

        if ($prefix >= 0xf8) {
            $lengthLength = $prefix - 0xf7;
            self::checkOffset($offset + $lengthLength, $data);

            $length = self::unarrayifyInteger($data, $offset + 1, $lengthLength);
            self::checkOffset($offset + 1 + $lengthLength + $length - 1, $data);

            return self::_decodeChildren($data, $offset, $offset + 1 + $lengthLength, $lengthLength + $length);
        } elseif ($prefix >= 0xc0) {
            $length = $prefix - 0xc0;
            if ($length > 0) {
                self::checkOffset($offset + 1 + $length - 1, $data);
            }
            return self::_decodeChildren($data, $offset, $offset + 1, $length);
        } elseif ($prefix >= 0xb8) {
            $lengthLength = $prefix - 0xb7;
            self::checkOffset($offset + $lengthLength, $data);

            $length = self::unarrayifyInteger($data, $offset + 1, $lengthLength);
            if ($length > 0) {
                self::checkOffset($offset + 1 + $lengthLength + $length - 1, $data);
            }
            $slice = array_slice($data, $offset + 1 + $lengthLength, $length);

            return [
                'consumed' => 1 + $lengthLength + $length,
                'result'   => self::hexlify($slice)
            ];
        } elseif ($prefix >= 0x80) {
            $length = $prefix - 0x80;
            if ($length > 0) {
                self::checkOffset($offset + 1 + $length - 1, $data);
            }
            $slice = array_slice($data, $offset + 1, $length);

            return [
                'consumed' => 1 + $length,
                'result'   => self::hexlify($slice)
            ];
        }

        return [
            'consumed' => 1,
            'result'   => self::hexlifyByte($prefix)
        ];
    }

    private static function checkOffset(int $offset, array $data): void
    {
        // We allow offset == count($data) to handle zero-length items.
        if ($offset > count($data)) {
            throw new InvalidArgumentException('data short segment or out of range');
        }
    }
}
