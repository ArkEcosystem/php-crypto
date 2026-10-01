<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\BLS;

use ArkEcosystem\Crypto\BLS\Curves\G1;
use ArkEcosystem\Crypto\BLS\HashToCurve\G2HashToCurve;
use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;
use ArkEcosystem\Crypto\Utils\Address;
use InvalidArgumentException;

final class ProofOfPossession
{
    public const POP_DST = 'MAINSAIL_BLS_POP_BLS12381G2_XMD:SHA-256_SSWU_RO_POP_';

    /**
     * Derives a BLS private key (32 bytes binary) from a BIP-39 mnemonic.
     */
    public static function deriveBlsPrivateKey(string $mnemonic): string
    {
        return EIP2333::deriveBlsPrivateKey($mnemonic);
    }

    /**
     * Derives the BLS12-381 G1 public key (48 bytes, ZCash compressed) from a mnemonic.
     * Returns lowercase hex (96 hex chars).
     */
    public static function deriveBlsPublicKey(string $mnemonic): string
    {
        $privKey = self::deriveBlsPrivateKey($mnemonic);

        return self::privateKeyToPublicKey($privKey);
    }

    /**
     * Returns the G1 public key hex for a given private key bytes.
     */
    public static function privateKeyToPublicKey(string $privateKeyBytes): string
    {
        $scalar = gmp_init(bin2hex($privateKeyBytes), 16);

        return G1::generator()->scalarMul($scalar)->toHex();
    }

    /**
     * Signs abi.encodePacked(uint256 chainId, address registrant, bytes pk) under POP_DST.
     * The registrant must be the transaction sender, as the contract verifies against msg.sender.
     *
     * @throws InvalidArgumentException
     * @return array{pk: string, pop: string}
     */
    public static function buildProofOfPossession(string $privateKeyBytes, int $chainId, string $registrantAddress): array
    {
        if ($chainId <= 0) {
            throw new InvalidArgumentException('Invalid chain id: '.$chainId);
        }

        if (! Address::validate($registrantAddress) || ! Address::hasValidChecksum($registrantAddress)) {
            throw new InvalidArgumentException('Invalid registrant address: '.$registrantAddress);
        }

        if (strlen($privateKeyBytes) !== 32) {
            throw new InvalidArgumentException(
                'BLS secret key must be exactly 32 bytes, got '.strlen($privateKeyBytes)
            );
        }

        $sk = gmp_init(bin2hex($privateKeyBytes), 16);

        if (gmp_sign($sk) === 0) {
            throw new InvalidArgumentException('BLS secret key must not be zero');
        }

        // G1 public key: [sk] * G1
        $pk = G1::generator()->scalarMul($sk)->toCompressedBytes();

        $message = ByteBuffer::new(0)
            ->writeUInt256($chainId)
            ->writeHex(substr($registrantAddress, 2))
            ->writeBytes($pk)
            ->toBinary();

        $messagePoint = G2HashToCurve::hashToG2($message, self::POP_DST);

        $pop = $messagePoint->scalarMul($sk)->toCompressedBytes();

        return [
            'pk'  => bin2hex($pk),
            'pop' => bin2hex($pop),
        ];
    }

    /**
     * Convenience: derive private key from mnemonic and build PoP.
     *
     * @return array{pk: string, pop: string}
     */
    public static function fromMnemonic(string $mnemonic, int $chainId, string $registrantAddress): array
    {
        return self::buildProofOfPossession(self::deriveBlsPrivateKey($mnemonic), $chainId, $registrantAddress);
    }
}
