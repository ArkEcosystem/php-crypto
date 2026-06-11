<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\BLS;

use ArkEcosystem\Crypto\BLS\Curves\G1;
use ArkEcosystem\Crypto\BLS\HashToCurve\G2HashToCurve;
use InvalidArgumentException;

/**
 * DST used for PoP signatures: "BLS_POP_BLS12381G2_XMD:SHA-256_SSWU_RO_POP_"
 */
final class ProofOfPossession
{
    public const POP_DST = 'BLS_POP_BLS12381G2_XMD:SHA-256_SSWU_RO_POP_';

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
     * Builds the Proof of Possession for a given private key.
     *
     * PoP = Sign(sk, Hash_G2(pk)) where Hash_G2 hashes the G1 public key bytes
     * to a G2 point under POP_DST, then the G2 point is "signed" by multiplying
     * by the private key scalar.
     *
     * @param  string $privateKeyBytes  32-byte raw private key
     * @throws InvalidArgumentException  if the key is not exactly 32 bytes or is the zero scalar
     * @return array{pk: string, pop: string}  hex-encoded G1 pk (96 chars) and G2 pop (192 chars)
     */
    public static function buildProofOfPossession(string $privateKeyBytes): array
    {
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

        // Hash pk bytes to G2 under POP_DST
        $messagePoint = G2HashToCurve::hashToG2($pk, self::POP_DST);

        // Sign: PoP = [sk] * hash(pk)
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
    public static function fromMnemonic(string $mnemonic): array
    {
        return self::buildProofOfPossession(self::deriveBlsPrivateKey($mnemonic));
    }
}
