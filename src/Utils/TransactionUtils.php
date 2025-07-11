<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\Utils;

use ArkEcosystem\Crypto\Configuration\Network;
use BI\BigInteger;
use BitWasp\Buffertools\Buffer;
use BitWasp\Buffertools\BufferInterface;
use Brick\Math\BigDecimal;
use kornrunner\Keccak;

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
            self::toBeArray(isset($transaction['nonce']) ? $transaction['nonce'] : 0),
            self::toBeArray($transaction['gasPrice'] ?? 0),
            self::toBeArray($transaction['gasLimit'] ?? 0),
            $transaction['to'] ?? '0x',
            self::toBeArray(isset($transaction['value']) ? $transaction['value'] : 0),
            isset($transaction['data']) && str_starts_with($transaction['data'], '0x')
                ? $transaction['data']
                : ('0x'.($transaction['data'] ?? '')),
        ];

        if (! $skipSignature && isset($transaction['v'], $transaction['r'], $transaction['s']) && ! empty($transaction['v'])) {
            $fields[] = self::toBeArray($transaction['v'] + Network::get()->chainId() * 2 + 35);
            $fields[] = '0x'.$transaction['r'];
            $fields[] = '0x'.$transaction['s'];
        } else {
            // Push chainId + 0s for r and s
            $fields[] = self::toBeArray(Network::get()->chainId());
            $fields[] = self::toBeArray(0);
            $fields[] = self::toBeArray(0);
            }
        }

        // TODO: second signature handling

        $encoded = RlpEncoder::encode($fields);

        $payload = Constants::EIP_1559_PREFIX.substr($encoded, 2);

        return new Buffer(hex2bin(substr($encoded, 2)));
    }

    /**
     * Generates the transaction hash.
     *
     * @param array $transaction The transaction data.
     * @param bool $skipSignature
     */
    public static function toHash(array $transaction, bool $skipSignature = false): BufferInterface
    {
        $encoding = self::toBuffer($transaction, $skipSignature);

        $keccak = Keccak::hash($encoding->getBinary(), 256);

        return Buffer::hex($keccak);
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
            if ($value === '0') {
                return '0x';
            }

            if (str_starts_with($value, '0x')) {
                return $value;
            }

            return '0x'.(new BigInteger($value, 10))->toHex();
        }

        if ($value instanceof BigDecimal) {
            if ((string) $value === '0') {
                return '0x';
            }

            return '0x'.(new BigInteger((string) $value, 10))->toHex();
        }

        return '0x';
    }
}
