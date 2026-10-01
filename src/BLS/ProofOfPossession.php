<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\BLS;

use ArkEcosystem\Crypto\BLS\Curves\G1;
use ArkEcosystem\Crypto\BLS\HashToCurve\G2HashToCurve;
use ArkEcosystem\Crypto\ByteBuffer\ByteBuffer;
use ArkEcosystem\Crypto\Utils\Address;
use InvalidArgumentException;

/**
 * DST used for PoP signatures: "MAINSAIL_BLS_POP_BLS12381G2_XMD:SHA-256_SSWU_RO_POP_".
 */
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
     * Builds the Proof of Possession for a given private key, bound to a chain and registrant.
     *
     * PoP = Sign(sk, Hash_G2(chainId ‖ registrantAddress ‖ pk)) where the message is
     * abi.encodePacked(uint256 chainId, address registrantAddress, bytes pk), hashed to
     * a G2 point under POP_DST, then "signed" by multiplying by the private key scalar.
     * The registrant must be the address that sends the registration/update transaction.
     *
     * @param  string $privateKeyBytes    32-byte raw private key
     * @param  int    $chainId            chain the proof is valid on
     * @param  string $registrantAddress  0x-prefixed address of the registering account
     * @throws InvalidArgumentException  if the key is not exactly 32 bytes or is the zero scalar,
     *                                   the chain id is not positive, or the address is invalid
     * @return array{pk: string, pop: string}  hex-encoded G1 pk (96 chars) and G2 pop (192 chars)
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

        // Sign: PoP = [sk] * hash(chainId ‖ registrantAddress ‖ pk)
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
