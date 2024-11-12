<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Utils;

use BitWasp\Bitcoin\Crypto\Hash;
use BitWasp\Buffertools\Buffer;
use BitWasp\Buffertools\BufferInterface;

class TransactionHasher
{
    /**
     * Generates the transaction hash.
     *
     * @param array $transaction The transaction data.
     * @param bool $skipSignature
     */
    public static function toHash(array $transaction, bool $skipSignature = false): BufferInterface
    {
        $hex              = ltrim($transaction['recipientAddress'], '0x');
        $hex              = str_pad($hex, strlen($hex) + (strlen($hex) % 2), '0', STR_PAD_LEFT);
        $recipientAddress = hex2bin($hex);

        // Build the fields array
        $fields = [
            self::toBeArray($transaction['network']),
            self::toBeArray($transaction['nonce']),
            self::toBeArray($transaction['gasPrice']), // maxPriorityFeePerGas
            self::toBeArray($transaction['gasPrice']), // maxFeePerGas
            self::toBeArray($transaction['gasLimit']),
            $recipientAddress,
            self::toBeArray($transaction['value']),
            isset($transaction['data']) ? hex2bin(ltrim($transaction['data'], '0x')) : '',
            [], // accessList is unused
        ];

        if (! $skipSignature) {
            $signatureBuffer = hex2bin($transaction['signature']);

            $r = substr($signatureBuffer, 0, 32);
            $s = substr($signatureBuffer, 32, 32);
            $v = ord($signatureBuffer[64]);

            $fields[] = self::toBeArray($v);
            $fields[] = $r;
            $fields[] = $s;
        }

        $eip1559Prefix = chr(0x02); // Marker for Type 2 (EIP1559) transaction

        $encoded = self::encodeRlp($fields);

        $hashInput = $eip1559Prefix.$encoded;

        // Use the SHA256 function from the BitWasp Bitcoin library
        return Hash::sha256(new Buffer($hashInput));
    }

    /**
     * Converts a big integer to a big-endian byte array.
     *
     * @param mixed $value The big integer value.
     * @return string The byte array as a string.
     */
    private static function toBeArray($value): string
    {
        if (is_int($value) || is_float($value)) {
            $value = (string) $value;
        }

        if (bccomp($value, '0') === 0) {
            return ''; // Empty string represents zero
        }

        $result = '';

        while (bccomp($value, '0') > 0) {
            $byte   = bcmod($value, '256');
            $result = chr((int) $byte).$result;
            $value  = bcdiv($value, '256', 0);
        }

        return $result;
    }

    /**
     * Encodes the length for RLP encoding.
     *
     * @param int $len The length to encode.
     * @return string The encoded length.
     */
    private static function encodeLength(int $len): string
    {
        $lenBytes = '';
        while ($len > 0) {
            $lenBytes = chr($len & 0xff).$lenBytes;
            $len >>= 8;
        }

        return $lenBytes;
    }

    /**
     * RLP encoding function.
     *
     * @param mixed $input The input to encode.
     * @return string The RLP-encoded string.
     */
    private static function encodeRlp($input): string
    {
        if (is_string($input)) {
            $len = strlen($input);
            if ($len === 1 && ord($input) <= 0x7f) {
                return $input;
            } elseif ($len <= 55) {
                return chr(0x80 + $len).$input;
            }
            $lenBytes = self::encodeLength($len);

            return chr(0xb7 + strlen($lenBytes)).$lenBytes.$input;
        } elseif (is_array($input)) {
            $output = '';
            foreach ($input as $item) {
                $output .= self::encodeRlp($item);
            }
            $len = strlen($output);
            if ($len <= 55) {
                return chr(0xc0 + $len).$output;
            }
            $lenBytes = self::encodeLength($len);

            return chr(0xf7 + strlen($lenBytes)).$lenBytes.$output;
        }

        // Handle numbers as big integers
        return self::encodeRlp(self::toBeArray($value));
    }
}
