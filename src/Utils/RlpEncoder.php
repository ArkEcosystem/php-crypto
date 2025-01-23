<?php

namespace ArkEcosystem\Crypto\Utils;

class RlpEncoder
{
    public static function decode(string $data): string|array
    {
        $decoded = self::_decode($data, 0);

        assert($decoded['consumed'] === strlen($data), "unexpected junk after rlp payload");

        return $decoded['result'];
    }

    private static function hexlifyByte(int $value): string
    {
        $result = dechex($value);
        while (strlen($result) < 2) {
            $result = "0" . $result;
        }

        return "0x" . $result;
    }

    private static function _decodeChildren(string $data, int $offset, int $childOffset, int $length): array
    {
        $result = [];

        while ($childOffset < $offset + 1 + $length) {
            $decoded = self::_decode($data, $childOffset);

            $result[] = $decoded['result'];

            $childOffset += $decoded['consumed'];
            assert($childOffset <= $offset + 1 + $length, "child data too short");
        }

        var_dump($result);

        return ['consumed' => ($childOffset - $offset), 'result' => $result];
    }

    private static function hexlify(string $data): string
    {
        return '0x' . bin2hex($data);
    }

    private static function unarrayifyInteger(string $data, int $offset, int $length): int
    {
        $result = 0;
        for ($i = 0; $i < $length; $i++) {
            $result = ($result << 8) + ord($data[$offset + $i]);
        }

        return $result;
    }

    private static function _decode(string $data, int $offset): string|array
    {
        assert(strlen($data) !== 0, "data too short");

        $checkOffset = function ($offset) use ($data) {
            assert($offset <= strlen($data), "data short segment too short");
        };

        var_dump($data[$offset], ord($data[$offset]), '', 0xf8, 0xc0, 0xb8, 0x80);

        if (ord($data[$offset]) >= 0xf8) {
            $lengthLength = ord($data[$offset]) - 0xf7;
            $checkOffset($offset + 1 + $lengthLength);

            $length = self::unarrayifyInteger($data, $offset + 1, $lengthLength);
            $checkOffset($offset + 1 + $lengthLength + $length);

            return self::_decodeChildren($data, $offset, $offset + 1 + $lengthLength, $lengthLength + $length);

        } elseif (ord($data[$offset]) >= 0xc0) {
            $length = ord($data[$offset]) - 0xc0;
            $checkOffset($offset + 1 + $length);

            return self::_decodeChildren($data, $offset, $offset + 1, $length);

        } elseif (ord($data[$offset]) >= 0xb8) {
            $lengthLength = ord($data[$offset]) - 0xb7;
            $checkOffset($offset + 1 + $lengthLength);

            $length = self::unarrayifyInteger($data, $offset + 1, $lengthLength);
            $checkOffset($offset + 1 + $lengthLength + $length);

            $result = self::hexlify(substr($data, $offset + 1 + $lengthLength, $length));

            var_dump($result);

            return ['consumed' => (1 + $lengthLength + $length), 'result' => $result];

        } elseif (ord($data[$offset]) >= 0x80) {
            $length = ord($data[$offset]) - 0x80;
            $checkOffset($offset + 1 + $length);

            $result = self::hexlify(substr($data, $offset + 1, $length));

            var_dump($result);

            return ['consumed' => (1 + $length), 'result' => $result];
        }

        return ['consumed' => 1, 'result' => self::hexlifyByte(ord($data[$offset]))];
    }

}
