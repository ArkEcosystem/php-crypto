<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Utils;

use BitWasp\Bitcoin\Crypto\Hash;
use BitWasp\Buffertools\Buffer;
use BitWasp\Buffertools\BufferInterface;

class TransactionUtils
{
    /**
     * Generates the transaction buffer.
     *
     * @param array $transaction The transaction data.
     * @param bool $skipSignature
     */
    public static function toBuffer(array $transaction, bool $skipSignature = false): Buffer
    {
        $fields = [
            self::toBeArray($transaction['network'] ?? 0),
            self::toBeArray(isset($transaction['nonce']) ? $transaction['nonce'] : 0),
            self::toBeArray(0),
            self::toBeArray($transaction['gasPrice'] ?? 0),
            self::toBeArray($transaction['gasLimit'] ?? 0),
            $transaction['recipientAddress'] ?? '0x',
            self::toBeArray(isset($transaction['value']) ? $transaction['value'] : 0),
            isset($transaction['data']) && str_starts_with($transaction['data'], '0x')
                ? $transaction['data']
                : ('0x' . ($transaction['data'] ?? '')),
            [],
        ];

        if (! $skipSignature) {
            if (isset($transaction['v'], $transaction['r'], $transaction['s'])) {
                $fields[] = self::toBeArray($transaction['v'] - 27);
                $fields[] = '0x' . $transaction['r'];
                $fields[] = '0x' . $transaction['s'];
            }
        }

        $encoded = RlpEncoder::encode($fields);

        $eip1559Prefix = '02'; // marker for Type 2 (EIP1559) transaction which is the standard nowadays

        $payload = $eip1559Prefix . substr($encoded, 2);

        return new Buffer(hex2bin($payload));
    }

    /**
     * Generates the transaction hash.
     *
     * @param array $transaction The transaction data.
     * @param bool $skipSignature
     */
    public static function toHash(array $transaction, bool $skipSignature = false): BufferInterface
    {
        return Hash::sha256(self::toBuffer($transaction, $skipSignature));
    }

    public static function getId(array $transaction): string
    {
        return self::toHash($transaction, false)->getHex();
    }

    /**
     * Converts a big integer to a big-endian byte array.
     *
     * @param mixed $value The big integer value.
     * @return string The byte array as a string.
     */
    private static function toBeArray(mixed $value): string
    {
        if (is_int($value)) {
            if ($value === 0) {
                return '0x';
            }

            return '0x'.dechex($value);
        }

        if (is_string($value)) {
            if (str_starts_with($value, '0x')) {
                return $value;
            }

            return '0x'.dechex((int) $value);
        }

        return '0x';
    }
}
